package com.panaceasoft.mokets.activities;

import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.design.widget.CoordinatorLayout;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.SpannableString;
import android.view.View;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.JsonObjectRequest;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.models.PItemData;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import org.json.JSONException;
import org.json.JSONObject;
import java.lang.reflect.Type;
import java.util.HashMap;

public class ReviewEntry extends AppCompatActivity {
    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private Toolbar toolbar;
    private SharedPreferences pref;
    private TextView txtUserName;
    private TextView txtUserEmail;
    private EditText txtReviewMessage;
    private int selectedItemId;
    private int selectedShopId;
    private ProgressBar pb;
    private String jsonStatusSuccessString;
    private SpannableString reviewString;
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
        setContentView(R.layout.activity_review_entry);

        initData();

        initUI();

        bindData();
    }

    @Override
    public void onBackPressed() {
        finish();
        overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
    }

    @Override
    public void onDestroy() {

        try {
            toolbar = null;
            pref = null;

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
        mainLayout = (CoordinatorLayout) findViewById(R.id.coordinator_layout);
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

            toolbar.setTitle(reviewString);
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initToolbar.", e);
        }
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void initData() {
        try {
            jsonStatusSuccessString = getResources().getString(R.string.json_status_success);
            reviewString = Utils.getSpannableString(getString(R.string.review));

            pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
            selectedItemId = getIntent().getIntExtra("selected_item_id", 0);
            selectedShopId = getIntent().getIntExtra("selected_shop_id", 0);
        }catch(Exception e){
            Utils.psErrorLogE("Error in init data.", e);
        }

    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/


    /**------------------------------------------------------------------------------------------------
     * Start Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void bindData() {
        try {
            txtUserName = (TextView) findViewById(R.id.login_user_name);
            txtUserEmail = (TextView) findViewById(R.id.login_user_email);

            txtUserName.setText(pref.getString("_login_user_name", "").toString());
            txtUserEmail.setText(pref.getString("_login_user_email", "").toString());
        } catch (Exception e) {
            Utils.psErrorLogE("Error in bindData.", e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/


    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    public void doReview(View view) {
        if (inputValidation()) {
            pb = (ProgressBar) findViewById(R.id.loading_spinner);
            pb.setVisibility(view.VISIBLE);

            final String URL = Config.APP_API_URL + Config.POST_REVIEW + getIntent().getExtras().getInt("selected_item_id");
            txtReviewMessage = (EditText) findViewById(R.id.input_review_message);
            txtReviewMessage.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            HashMap<String, String> params = new HashMap<>();
            params.put("review", txtReviewMessage.getText().toString().trim());
            params.put("appuser_id", String.valueOf(pref.getInt("_login_user_id", 0)));
            params.put("shop_id", String.valueOf(pref.getInt("_id", 0)));

            doSubmit(URL, params, view);
        }

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    private void doSubmit(String postURL, HashMap<String, String> params, final View view) {

        Utils.psLog(" URL : " + postURL);
        Utils.psLog(" params : " + params);

        JsonObjectRequest req = new JsonObjectRequest(postURL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String success_status = response.getString("status");

                            if (success_status.equals(jsonStatusSuccessString)) {
                                Gson gson = new Gson();
                                Type listType = new TypeToken<PItemData>() {
                                }.getType();
                                GlobalData.itemData = (PItemData) gson.fromJson(response.getString("data"), listType);

                                pb = (ProgressBar) findViewById(R.id.loading_spinner);
                                pb.setVisibility(View.GONE);

                                Utils.psLog(success_status);
                                if (success_status != null) {
                                    Utils.psLog(" --- Need to refresh review list and count --- ");
                                    //showSuccessPopup();
                                    Intent in = new Intent();
                                    setResult(RESULT_OK, in);
                                    finish();
                                } else {
                                    showFailPopup();
                                }
                            } else {

                                Utils.psLog("Error in loading.");
                            }


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
        // add the request object to the queue to be executed
        VolleySingleton.getInstance(this).addToRequestQueue(req);
    }

    private void showFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.review);
        builder.setMessage(R.string.register_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    private void showSuccessPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.review);
        builder.setMessage(R.string.review_success);
        builder.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                finish();
                Utils.psLog("OK clicked.");
            }
        });
        builder.show();
    }

    private boolean inputValidation() {
        txtReviewMessage = (EditText) findViewById(R.id.input_review_message);

        if (txtReviewMessage.getText().toString().equals("")) {
            Toast.makeText(getApplicationContext(), R.string.review_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }

        return true;

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

}
