package com.panaceasoft.mokets.activities;

import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.design.widget.CoordinatorLayout;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.SpannableString;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.JsonObjectRequest;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.models.BasketData;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class CheckoutConfirmActivity extends AppCompatActivity{
    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private Toolbar toolbar;
    private Intent intent;
    private int selectedShopId;
    private String jsonStatusSuccessString;
    private SpannableString checkoutConfirmString;
    private SharedPreferences pref;
    private String selectedPayemntOption;
    private Bundle bundle;
    private TextView txtTotalAmount;
    private TextView txtSelectedPayment;
    private EditText etUserName;
    private EditText etUserEmail;
    private EditText etUserPhone;
    private EditText etUserDeliveryAddress;
    private EditText etUserBillingAddress;
    private Button btnConfirmCheckout;
    private List<BasketData> basket;
    private List<BasketData> basketDataSet;
    DBHandler db = new DBHandler(this);
    private HashMap<String, String> params = new HashMap<String, String>();
    private String URL = "";
    private ProgressDialog prgDialog;
    private CoordinatorLayout mainLayout;


    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_checkout_confirm);
        pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());

        initUI();
        initData();
        bindData();

    }

    @Override
    public void onBackPressed() {
        finish();
        overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {

        if(requestCode == 1){

            if(resultCode == RESULT_OK){

                if(data.getStringExtra("close_activity").equals("YES")){
                    Intent in = new Intent();
                    in.putExtra("close_activity", "YES");
                    setResult(RESULT_OK, in);
                    finish();
                }
            }

        }

    }

    @Override
    public void onDestroy() {
        try {
            toolbar = null;
            pref = null;
            prgDialog.cancel();
            prgDialog = null;

            Utils.unbindDrawables(mainLayout);
            mainLayout = null;

            super.onDestroy();
        }catch (Exception e){
            super.onDestroy();
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    private void initUI() {
        initToolbar();
        initProgressDialog();
        mainLayout = (CoordinatorLayout) findViewById(R.id.coordinator_layout);

        txtTotalAmount = (TextView) findViewById(R.id.total_amount);
        txtTotalAmount.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        txtSelectedPayment = (TextView) findViewById(R.id.selected_payment);
        txtSelectedPayment.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        etUserName = (EditText) findViewById(R.id.input_name);
        etUserName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        etUserEmail = (EditText) findViewById(R.id.input_email);
        etUserEmail.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        etUserPhone = (EditText) findViewById(R.id.input_phone);
        etUserPhone.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        etUserDeliveryAddress = (EditText) findViewById(R.id.input_delivery_address);
        etUserDeliveryAddress.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        etUserBillingAddress = (EditText) findViewById(R.id.input_billing_address);
        etUserBillingAddress.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        btnConfirmCheckout = (Button) findViewById(R.id.btn_confirm_checkout);
        btnConfirmCheckout.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

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
    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void initData(){
        try {
            selectedShopId = getIntent().getIntExtra("selected_shop_id", 0);
            bundle = getIntent().getExtras();
            selectedPayemntOption = bundle.getString("selected_payment_option");
            txtTotalAmount.setText(getString(R.string.total_amount) + BasketActivity.totalAmount + GlobalData.shopdata.currency_symbol + "(" + GlobalData.shopdata.currency_short_form + ")");

            if(selectedPayemntOption.equals("stripe")) {
                txtSelectedPayment.setText(getString(R.string.stripe));
            } else if(selectedPayemntOption.equals("bank")) {
                txtSelectedPayment.setText(getString(R.string.bank_transfer));
            } else if(selectedPayemntOption.equals("cod")) {
                txtSelectedPayment.setText(getString(R.string.cod));
            }

            etUserName.setText(pref.getString("_login_user_name", ""));
            etUserEmail.setText(pref.getString("_login_user_email", ""));
            etUserPhone.setText(pref.getString("_login_user_phone", ""));
            etUserDeliveryAddress.setText(pref.getString("_login_user_delivery_address", ""));
            etUserBillingAddress.setText(pref.getString("_login_user_billing_address", ""));


            jsonStatusSuccessString = getResources().getString(R.string.json_status_success);
            checkoutConfirmString = Utils.getSpannableString(getString(R.string.title_checkout_confirm));



        } catch (Resources.NotFoundException e) {
            Utils.psErrorLogE("Error in initToolbar.", e);
        }
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/


    /**------------------------------------------------------------------------------------------------
     * Start Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void bindData() {
        toolbar.setTitle(checkoutConfirmString);
    }

    private void submitOrderToServer() {
        prgDialog.show();
        URL = Config.APP_API_URL + Config.POST_TRANSACTIONS;
        Utils.psLog(" >>> here >>> " + URL);
        Utils.psLog(">> params >> " + params);

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

    private void loadBasketData(String paymentOption) {
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
                jsonObject.put("delivery_address", etUserDeliveryAddress.getText().toString());
                jsonObject.put("billing_address", etUserBillingAddress.getText().toString());
                jsonObject.put("total_amount", BasketActivity.totalAmount);
                jsonObject.put("basket_item_attribute", Utils.removeLastChar(basketData.getSelectedAttributeNames()));
                jsonObject.put("payment_method", paymentOption);
                jsonObject.put("email", etUserEmail.getText().toString());
                jsonObject.put("phone", etUserPhone.getText().toString());

                jsonArray.put(jsonObject);
            } catch (JSONException e) {
                e.printStackTrace();
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
        }

        Utils.psLog(" >>> params >> " + params);

    }

    private void closeActivities() {
        Utils.psLog(" >>> CheckoutConfirmActivity >>> closeActivities ???  ");
        Intent in = new Intent();
        in.putExtra("close_activity", "YES");
        setResult(RESULT_OK, in);
        finish();
        overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    public void doConfirmCheckout(View view) {

        if(selectedPayemntOption.equals("stripe")) {
            intent = new Intent(getApplicationContext(), StripeActivity.class);
            intent.putExtra("selected_shop_id", selectedShopId);
            intent.putExtra("selected_payment_option", "stripe");
            intent.putExtra("user_email", etUserEmail.getText().toString());
            intent.putExtra("user_phone", etUserPhone.getText().toString());
            intent.putExtra("user_delivery_address", etUserDeliveryAddress.getText().toString());
            intent.putExtra("user_billing_address", etUserBillingAddress.getText().toString());
            startActivityForResult(intent, 1);
            overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);

        } else if(selectedPayemntOption.equals("bank")) {
            Utils.psLog(" selected bank >>> ");
            loadBasketData("bank");
            submitOrderToServer();
        } else if(selectedPayemntOption.equals("cod")) {
            Utils.psLog(" selected cod >>> ");
            loadBasketData("cod");
            submitOrderToServer();
        }


    }

    public void showSuccessPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.order_submit_title);
        builder.setMessage(R.string.order_success);
        builder.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                closeActivities();
                Utils.psLog("OK clicked.");
            }
        });
        builder.show();
    }

    public void showFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.order_submit_title);
        builder.setMessage(R.string.order_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

}
