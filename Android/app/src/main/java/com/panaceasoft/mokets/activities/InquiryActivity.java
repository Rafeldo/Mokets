package com.panaceasoft.mokets.activities;

import android.app.ProgressDialog;
import android.content.DialogInterface;
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
import android.widget.EditText;
import android.widget.Toast;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.JsonObjectRequest;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import android.widget.Button;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.HashMap;


public class InquiryActivity extends AppCompatActivity {

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private Toolbar toolbar;
    private EditText txtName;
    private EditText txtEmail;
    private EditText txtMessage;
    private SharedPreferences pref;
    private ProgressDialog prgDialog;
    private String jsonStatusSuccessString;
    private SpannableString inquiry;
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
        setContentView(R.layout.activity_inquiry);

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
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void initData() {
        try {
            pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
            jsonStatusSuccessString = getResources().getString(R.string.json_status_success);
            inquiry = Utils.getSpannableString(getString(R.string.inquiry));
        } catch (Resources.NotFoundException e) {
            Utils.psErrorLog("Error in initData," ,e );
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    private void initUI() {

        try {
            initToolbar();
            initProgressDialog();
            mainLayout = (CoordinatorLayout) findViewById(R.id.coordinator_layout);
            txtName = (EditText) findViewById(R.id.input_name);
            txtName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            txtEmail = (EditText) findViewById(R.id.input_email);
            txtEmail.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            txtMessage = (EditText) findViewById(R.id.input_message);
            txtMessage.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            Button btnSubmit = (Button) findViewById(R.id.button_submit);
            btnSubmit.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initUI.", e);
        }
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
     * Start Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void bindData() {
        toolbar.setTitle(inquiry);
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/


    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    public void doInquiry(View view) {
        if (inputValidation()) {

            final String URL = Config.APP_API_URL + Config.POST_ITEM_INQUIRY + GlobalData.itemData.id;
            Utils.psLog(URL);

            HashMap<String, String> params = new HashMap<>();
            params.put("name", txtName.getText().toString());
            params.put("email", txtEmail.getText().toString());
            params.put("message", txtMessage.getText().toString());
            params.put("shop_id", String.valueOf(pref.getInt("_id", 0)));
            doSubmit(URL, params);
        }
    }

    public void doSubmit(String URL, final HashMap<String, String> params) {
        prgDialog.show();
        JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                          //  pb.setVisibility(view.GONE);

                            String status = response.getString("status");
                            if (status.equals(jsonStatusSuccessString)) {
                                Utils.psLog(status);

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

    public void showSuccessPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.inquiry);
        builder.setMessage(R.string.inquiry_success);
        builder.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                finish();
                Utils.psLog("OK clicked.");
            }
        });
        builder.show();
    }

    public void showFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.inquiry);
        builder.setMessage(R.string.inquiry_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    public boolean inputValidation() {

        if (txtName.getText().toString().equals("")) {
            Toast.makeText(getApplicationContext(), R.string.name_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }

        if (txtEmail.getText().toString().equals("")) {
            Toast.makeText(getApplicationContext(), R.string.email_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        } else {
            if (!Utils.isEmailFormatValid(txtEmail.getText().toString())) {
                Toast.makeText(getApplicationContext(), R.string.email_format_validation_message,
                        Toast.LENGTH_LONG).show();
                return false;
            }
        }

        if (txtMessage.getText().toString().equals("")) {
            Toast.makeText(getApplicationContext(), R.string.inquiry_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }

        return true;
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/


}
