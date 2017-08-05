package com.panaceasoft.mokets.activities;

import android.app.ProgressDialog;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.design.widget.CoordinatorLayout;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.Editable;
import android.text.SpannableString;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

import com.afollestad.materialdialogs.MaterialDialog;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.models.BasketData;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import com.stripe.android.Stripe;
import com.stripe.android.TokenCallback;
import com.stripe.android.model.Card;
import com.stripe.android.exception.AuthenticationException;
import butterknife.ButterKnife;
import butterknife.OnTextChanged;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.JsonObjectRequest;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class StripeActivity extends AppCompatActivity{

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Private Variables
    //-------------------------------------------------------------------------------------------------------------------------------------
    private Toolbar toolbar;
    private SpannableString stripeString;

    private static final int CARD_NUMBER_TOTAL_SYMBOLS = 19; // size of pattern 0000-0000-0000-0000
    private static final int CARD_NUMBER_TOTAL_DIGITS = 16; // max numbers of digits in pattern: 0000 x 4
    private static final int CARD_NUMBER_DIVIDER_MODULO = 5; // means divider position is every 5th symbol beginning with 1
    private static final int CARD_NUMBER_DIVIDER_POSITION = CARD_NUMBER_DIVIDER_MODULO - 1; // means divider position is every 4th symbol beginning with 0
    private static final char CARD_NUMBER_DIVIDER = '-';

    private static final int CARD_DATE_TOTAL_SYMBOLS = 5; // size of pattern MM/YY
    private static final int CARD_DATE_TOTAL_DIGITS = 4; // max numbers of digits in pattern: MM + YY
    private static final int CARD_DATE_DIVIDER_MODULO = 3; // means divider position is every 3rd symbol beginning with 1
    private static final int CARD_DATE_DIVIDER_POSITION = CARD_DATE_DIVIDER_MODULO - 1; // means divider position is every 2nd symbol beginning with 0
    private static final char CARD_DATE_DIVIDER = '/';

    private static final int CARD_CVC_TOTAL_SYMBOLS = 3;


    public static final String PUBLISHABLE_KEY = GlobalData.shopdata.stripe_publishable_key;
    public static final String API_KEY = GlobalData.shopdata.stripe_secret_key;

    private String URL = "";
    private String stripeToken;
    DBHandler db = new DBHandler(this);
    private List<BasketData> basket;
    private List<BasketData> basketDataSet;
    private String json_data = "";
    private String deliveryAddress = "";
    private String billingAddress = "";
    private String email = "";
    private String phone = "";

    //private RequestQueue mRequestQueue;
    private HashMap<String, String> params = new HashMap<String, String>();
    private EditText etCreditCard;
    private EditText etDate;
    private EditText etCVC;
    private Bundle bundle;
    private String jsonStatusSuccessString;
    private ProgressDialog prgDialog;
    private int selectedShopId;
    private CoordinatorLayout mainLayout;



    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Private Variables
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Override Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        //mRequestQueue = Volley.newRequestQueue(this);
        setContentView(R.layout.activity_stripe);
        ButterKnife.bind(this);

        initData();
        initUI();
        bindData();

    }

    @OnTextChanged(value = R.id.cardNumberEditText, callback = OnTextChanged.Callback.AFTER_TEXT_CHANGED)
    protected void onCardNumberTextChanged(Editable s) {
        if (!isInputCorrect(s, CARD_NUMBER_TOTAL_SYMBOLS, CARD_NUMBER_DIVIDER_MODULO, CARD_NUMBER_DIVIDER)) {
            s.replace(0, s.length(), concatString(getDigitArray(s, CARD_NUMBER_TOTAL_DIGITS), CARD_NUMBER_DIVIDER_POSITION, CARD_NUMBER_DIVIDER));
        }
    }

    @OnTextChanged(value = R.id.cardDateEditText, callback = OnTextChanged.Callback.AFTER_TEXT_CHANGED)
    protected void onCardDateTextChanged(Editable s) {
        if (!isInputCorrect(s, CARD_DATE_TOTAL_SYMBOLS, CARD_DATE_DIVIDER_MODULO, CARD_DATE_DIVIDER)) {
            s.replace(0, s.length(), concatString(getDigitArray(s, CARD_DATE_TOTAL_DIGITS), CARD_DATE_DIVIDER_POSITION, CARD_DATE_DIVIDER));
        }
    }

    @OnTextChanged(value = R.id.cardCVCEditText, callback = OnTextChanged.Callback.AFTER_TEXT_CHANGED)
    protected void onCardCVCTextChanged(Editable s) {
        if (s.length() > CARD_CVC_TOTAL_SYMBOLS) {
            s.delete(CARD_CVC_TOTAL_SYMBOLS, s.length());
        }
    }

    @Override
    public void onDestroy() {
        try {
            toolbar = null;
            prgDialog.cancel();
            prgDialog = null;

            Utils.unbindDrawables(mainLayout);
            mainLayout = null;

            super.onDestroy();
        }catch (Exception e){
            super.onDestroy();
        }
    }


    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Override Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Init Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void initData() {

        stripeString = Utils.getSpannableString(getString(R.string.title_stripe));
        jsonStatusSuccessString = getResources().getString(R.string.json_status_success);
        bundle = getIntent().getExtras();
        email = bundle.getString("user_email");
        phone = bundle.getString("user_phone");
        selectedShopId = getIntent().getIntExtra("selected_shop_id", 0);
        deliveryAddress = bundle.getString("user_delivery_address");
        billingAddress = bundle.getString("user_billing_address");

        loadBasketData();


    }
    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion init Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // init UI Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void initUI() {

        initToolbar();
        initProgressDialog();

        mainLayout = (CoordinatorLayout) findViewById(R.id.coordinator_layout);

        etCreditCard = (EditText) findViewById(R.id.cardNumberEditText);
        etDate = (EditText) findViewById(R.id.cardDateEditText);
        etCVC = (EditText) findViewById(R.id.cardCVCEditText);

    }

    private void initToolbar() {
        try {
            toolbar = (Toolbar) findViewById(R.id.toolbar);
            toolbar.setTitle("");
            setSupportActionBar(toolbar);
            toolbar.setNavigationOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });
            if(getSupportActionBar() != null) {
                getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            }
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initToolbar.", e);
        }
    }

    private void initProgressDialog() {
        try {
            prgDialog = new ProgressDialog(this);
            prgDialog.setMessage("Please wait...");
            prgDialog.setCancelable(false);
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initProgressDialog.", e);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion init UI Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Bind Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void bindData() {
        toolbar.setTitle(stripeString);
    }
    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Bind Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------


    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Private Functions
    //-------------------------------------------------------------------------------------------------------------------------------------


    private boolean isInputCorrect(Editable s, int size, int dividerPosition, char divider) {
        boolean isCorrect = s.length() <= size;
        for (int i = 0; i < s.length(); i++) {
            if (i > 0 && (i + 1) % dividerPosition == 0) {
                isCorrect &= divider == s.charAt(i);
            } else {
                isCorrect &= Character.isDigit(s.charAt(i));
            }
        }
        return isCorrect;
    }

    private String concatString(char[] digits, int dividerPosition, char divider) {
        final StringBuilder formatted = new StringBuilder();

        for (int i = 0; i < digits.length; i++) {
            if (digits[i] != 0) {
                formatted.append(digits[i]);
                if ((i > 0) && (i < (digits.length - 1)) && (((i + 1) % dividerPosition) == 0)) {
                    formatted.append(divider);
                }
            }
        }

        return formatted.toString();
    }

    private char[] getDigitArray(final Editable s, final int size) {
        char[] digits = new char[size];
        int index = 0;
        for (int i = 0; i < s.length() && index < size; i++) {
            char current = s.charAt(i);
            if (Character.isDigit(current)) {
                digits[index] = current;
                index++;
            }
        }
        return digits;
    }

    public void submitOrderToServer() {
        URL = Config.APP_API_URL + Config.POST_TRANSACTIONS;


        JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                       Utils.psLog(" sever resp : " + response);

                        try {
                            //  pb.setVisibility(view.GONE);

                            String status = response.getString("status");
                            if (status.equals(jsonStatusSuccessString)) {
                                Utils.psLog(status);
                                //Need to clear basket sqlite data
                                db.deleteBasketByShopId(selectedShopId);

                                //Need to close all activities and load home page
                                //closeActivities();

                                showSuccessPopup();

                            } else {
                                showFailPopup();
                                Utils.psLog("Error in loading.");
                            }

                            prgDialog.cancel();

                        } catch (JSONException e) {
                            e.printStackTrace();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }

                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                VolleyLog.e("Error: ", error.getMessage());
            }
        });

        req.setShouldCache(false);
        VolleySingleton.getInstance(this).addToRequestQueue(req);

    }


    private void loadBasketData() {
        basketDataSet = new ArrayList<>();
        basketDataSet.clear();
        basket = db.getAllBasketDataByShopId(selectedShopId);

        JSONArray jsonArray = new JSONArray();

        JSONObject jsonObject = null;

        for (BasketData basketData : basket) {


            try {
                jsonObject = new JSONObject();

                jsonObject.put("item_id", String.valueOf(basketData.getItemId()));
                jsonObject.put("shop_id", String.valueOf(basketData.getShopId()));
                jsonObject.put("unit_price", basketData.getUnitPrice());
                jsonObject.put("discount_percent", basketData.getDiscountPercent());
                jsonObject.put("name", basketData.getName());
                jsonObject.put("qty", basketData.getQty());
                jsonObject.put("user_id", basketData.getUserId());
                jsonObject.put("payment_trans_id", "");
                jsonObject.put("delivery_address", deliveryAddress);
                jsonObject.put("billing_address", billingAddress);
                jsonObject.put("total_amount", BasketActivity.totalAmount);
                jsonObject.put("basket_item_attribute", Utils.removeLastChar(basketData.getSelectedAttributeNames()));
                jsonObject.put("payment_method", "stripe");
                jsonObject.put("email", email);
                jsonObject.put("phone", phone);

                jsonArray.put(jsonObject);
            } catch (JSONException e) {
                e.printStackTrace();
            } catch (Exception e){
                Utils.psErrorLog("Error in loading.", e);
            }

        }


        JSONObject studentsObj = new JSONObject();
        String jsonStr = "";
        try {

            studentsObj.put("Orders", jsonArray);
            jsonStr = studentsObj.toString();
            Utils.psLog(" json >>> " + jsonStr);

            params.put("orders", jsonArray.toString());

        } catch (JSONException e) {
            e.printStackTrace();
        } catch (Exception e){
            Utils.psErrorLog("Error in loading.", e);
        }

        Utils.psLog(" >>> params >> " + params);

    }

    private void closeActivities() {
        Intent in = new Intent();
        in.putExtra("close_activity", "YES");
        setResult(RESULT_OK, in);
        finish();
        overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Private Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Public Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    public void doStripePayment(View view) throws AuthenticationException {




        if(etCreditCard.getText().toString().equals("") | etDate.getText().toString().equals("")) {

            Utils.psLog("Empty if");

        } else {
            prgDialog.show();
            if(etCVC.getText().toString().equals("")) {
                Utils.psLog("Empty");
            } else {

                String[] explodedDate = etDate.getText().toString().split("/");
                String year = "20" + explodedDate[1];


                Card card = new Card(etCreditCard.getText().toString(), Integer.parseInt(explodedDate[0]), Integer.parseInt(year), etCVC.getText().toString());


                if ( !card.validateCard() ) {
                    Toast.makeText(this, "Invalid Credit Card", Toast.LENGTH_SHORT).show();
                    return;
                }

                Stripe stripe = new Stripe(PUBLISHABLE_KEY);
                stripe.createToken(
                        card,
                        new TokenCallback() {
                            @Override
                            public void onError(Exception e) {
                                // Show localized error message
                                Toast.makeText(StripeActivity.this,
                                        e.getLocalizedMessage(),
                                        Toast.LENGTH_LONG
                                ).show();
                            }

                            @Override
                            public void onSuccess(com.stripe.android.model.Token token) {
                                //chargeCustomer(token);

                                final String URL = Config.APP_API_URL + Config.POST_STRIPE_TOKEN;
                                Utils.psLog(URL);

                                HashMap<String, String> params = new HashMap<>();
                                params.put("stripeToken", String.valueOf(token.getId()));
                                params.put("amount", String.valueOf(BasketActivity.totalAmount));
                                params.put("currency", GlobalData.shopdata.currency_short_form);
                                params.put("shopId", String.valueOf(selectedShopId));


                                doSubmit(URL, params);
                            }

                            /*
                            public void chargeCustomer(com.stripe.android.model.Token token) {
                                final Map<String, Object> chargeParams = new HashMap<String, Object>();
                                chargeParams.put("amount", (int) (BasketActivity.totalAmount * 100));
                                chargeParams.put("currency", GlobalData.shopdata.currency_short_form.toLowerCase());
                                chargeParams.put("card", token.getId());
                                chargeParams.put("description", "Mokets Order From Android");
                                Utils.psLog("token : " + token.getId());
                                new AsyncTask<Void, Void, Void>() {

                                    Charge charge;

                                    @Override
                                    protected Void doInBackground(Void... params) {
                                        try {
                                            com.stripe.Stripe.apiKey = API_KEY;
                                            charge = Charge.create(chargeParams);

                                        } catch (Exception e) {
                                            // TODO Auto-generated catch block
                                            e.printStackTrace();
                                        }
                                        return null;
                                    }

                                    protected void onPostExecute(Void result) {
                                        submitOrderToServer();
                                    };

                                }.execute();
                            }
                            */


                        }
                );

            }

        }



    }

    public void doSubmit(String URL, final HashMap<String, String> params) {
        Utils.psLog("Stripe Params " + params);
        prgDialog.show();
        JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        prgDialog.cancel();
                        try {
                            //  pb.setVisibility(view.GONE);

                            String status = response.getString("status");
                            if (status.equals(jsonStatusSuccessString)) {
                                Utils.psLog(status);
                                submitOrderToServer();

                            } else {
                                showFailPopup();
                                Utils.psLog("Error in loading.");
                            }

                            prgDialog.cancel();

                        } catch (JSONException e) {
                            e.printStackTrace();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                VolleyLog.e("Error: ", error.getMessage());
            }
        });

        req.setShouldCache(false);
        VolleySingleton.getInstance(this).addToRequestQueue(req);
    }

    public void showSuccessPopup() {
        new MaterialDialog.Builder(this)
                .title(R.string.order_submit_title)
                .content(R.string.order_success)
                .positiveText(R.string.OK)
                .callback(new MaterialDialog.ButtonCallback() {
                    public void onPositive(MaterialDialog dialog) {
                        //finish();
                        closeActivities();
                    }
                })
                .show();
    }

    public void showFailPopup() {
        new MaterialDialog.Builder(this)
                .title(R.string.order_submit_title)
                .content(R.string.order_fail)
                .positiveText(R.string.OK)
                .show();
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Public Functions
    //-------------------------------------------------------------------------------------------------------------------------------------


}
