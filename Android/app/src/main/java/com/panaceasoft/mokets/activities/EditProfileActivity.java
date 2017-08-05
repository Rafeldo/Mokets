package com.panaceasoft.mokets.activities;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.ContextWrapper;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Environment;
import android.preference.PreferenceManager;
import android.provider.MediaStore;
import android.support.design.widget.CoordinatorLayout;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.text.SpannableString;
import android.util.Base64;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.Toast;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

public class EditProfileActivity extends AppCompatActivity {

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private Toolbar toolbar;
    private EditText etUserName;
    private EditText etEmail;
    private EditText etAboutMe;
    private EditText etUserPhone;
    private EditText etUserDeliveryAddress;
    private EditText etUserBillingAddress;
    private ImageView ivProfilePhoto;
    private SharedPreferences pref;
    private int userId;
    private static int RESULT_LOAD_IMAGE = 1;
    private String encodedString;
    private String fileName;
    private Bitmap myImg;
    private ProgressDialog prgDialog;
    private SpannableString editProfileString;
    private String jsonStatusSuccessString ;
    private String photoUploadSuccess;
    private String photoUploadNotSuccess;
    private CoordinatorLayout mainLayout;
    //private ProgressBar pb;

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_edit_profile);

        initData();

        initUI();

        bindData();

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_edit_profile, menu);

        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.action_settings) {
            final Intent intent;
            intent = new Intent(this, PasswordUpdateActivity.class);
            startActivity(intent);
            overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        overridePendingTransition(R.anim.blank_anim,R.anim.left_to_right);

    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        try {
            if (requestCode == RESULT_LOAD_IMAGE && resultCode == RESULT_OK && null != data) {
                Uri selectedImage = data.getData();
                String[] filePathColumn = {MediaStore.Images.Media.DATA};

                Cursor cursor = getContentResolver().query(selectedImage,
                        filePathColumn, null, null, null);

                if(cursor != null) {
                    cursor.moveToFirst();

                    int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
                    String picturePath = cursor.getString(columnIndex);
                    cursor.close();

                    String fileNameSegments[] = picturePath.split("/");
                    fileName = fileNameSegments[fileNameSegments.length - 1];

                    myImg = Bitmap.createBitmap(getResizedBitmap(Utils.getUnRotatedImage(picturePath, BitmapFactory.decodeFile(picturePath)), 400));
                    ByteArrayOutputStream stream = new ByteArrayOutputStream();
                    myImg.compress(Bitmap.CompressFormat.JPEG, 80, stream);
                    byte[] byte_arr = stream.toByteArray();
                    encodedString = Base64.encodeToString(byte_arr, 0);
                    uploadImage();
                }

            }

        }catch(Exception e){
            Utils.psErrorLogE("Error in load image.", e);
        }

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
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/
    private void initData(){
        try {
            pref = PreferenceManager.getDefaultSharedPreferences(this.getBaseContext());
            userId = pref.getInt("_login_user_id", 0);

            editProfileString = Utils.getSpannableString(getString(R.string.edit_profile));

            jsonStatusSuccessString = getResources().getString(R.string.json_status_success);
            photoUploadSuccess = getResources().getString(R.string.photo_upload_success);
            photoUploadNotSuccess = getResources().getString(R.string.photo_upload_not_success);
        } catch (Resources.NotFoundException e) {
            Utils.psErrorLogE("Error in initData.", e);
        }

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/
    private void initUI(){
        try {
            initToolbar();

            mainLayout = (CoordinatorLayout) findViewById(R.id.coordinator_layout);
            etUserName = (EditText) findViewById(R.id.input_name);
            etUserName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            etEmail = (EditText) findViewById(R.id.input_email);
            etEmail.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            etAboutMe = (EditText) findViewById(R.id.input_about_me);
            etAboutMe.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            etUserPhone = (EditText) findViewById(R.id.input_phone);
            etUserPhone.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            etUserBillingAddress = (EditText) findViewById(R.id.input_billing_address);
            etUserBillingAddress.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            etUserDeliveryAddress = (EditText) findViewById(R.id.input_delivery_address);
            etUserDeliveryAddress.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            ivProfilePhoto = (ImageView) findViewById(R.id.fab);
            ivProfilePhoto.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    try {
                        Intent i = new Intent(
                                Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);

                        startActivityForResult(i, RESULT_LOAD_IMAGE);
                    }catch (Exception e){
                        Utils.psErrorLogE("Error in Image Gallery.", e);
                    }
                }
            });

            prgDialog = new ProgressDialog(this);
            prgDialog.setMessage("Please wait...");
            prgDialog.setCancelable(false);
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

    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/
    private void bindData() {
        try {
            etUserName.setText(pref.getString("_login_user_name", ""));
            etEmail.setText(pref.getString("_login_user_email", ""));
            etAboutMe.setText(pref.getString("_login_user_about_me", ""));
            etUserPhone.setText(pref.getString("_login_user_phone", ""));
            etUserBillingAddress.setText(pref.getString("_login_user_billing_address",""));
            etUserDeliveryAddress.setText(pref.getString("_login_user_delivery_address", ""));


            File file;

            ContextWrapper cw = new ContextWrapper(Utils.activity.getApplicationContext());
            File directory = cw.getDir("imageDir", Context.MODE_APPEND);
            file = new File(directory, pref.getString("_login_user_photo", ""));

            if (file.exists()) {

                Bitmap myBitmap = BitmapFactory.decodeFile(file.getAbsolutePath());

                ivProfilePhoto.setImageBitmap(myBitmap);

            } else {
                Drawable myDrawable = ContextCompat.getDrawable(this, R.drawable.ic_person_black);
                ivProfilePhoto.setImageDrawable(myDrawable);
            }

            toolbar.setTitle(editProfileString);

        }catch(Exception e){
            Utils.psErrorLogE("Error in Bind Data.", e);
        }
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/
    private boolean inputValidation() {

        if (etUserName.getText().toString().trim().equals("")) {
            Toast.makeText(getApplicationContext(), R.string.name_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }

        if (etEmail.getText().toString().trim().equals("")) {
            Toast.makeText(getApplicationContext(), R.string.email_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }
        return true;
    }

    private void doSubmit(String postURL, HashMap<String, String> params) {
        prgDialog.show();
        JsonObjectRequest req = new JsonObjectRequest(Request.Method.PUT, postURL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String status = response.getString("status");
                            Utils.psLog("Edit Profile Response : "+ response);
                            Utils.psLog("Edit Profile Status : "+ status);
                            if (status.equals(jsonStatusSuccessString)) {

                                // after server success
                                SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getApplicationContext());
                                SharedPreferences.Editor editor = prefs.edit();
                                editor.putString("_login_user_name", etUserName.getText().toString().trim());
                                editor.putString("_login_user_email", etEmail.getText().toString().trim());
                                editor.putString("_login_user_about_me", etAboutMe.getText().toString().trim());
                                editor.putString("_login_user_phone", etUserPhone.getText().toString().trim());
                                editor.putString("_login_user_billing_address", etUserBillingAddress.getText().toString().trim());
                                editor.putString("_login_user_delivery_address", etUserDeliveryAddress.getText().toString().trim());


                                editor.apply();

                                prgDialog.cancel();
                                showSuccessMessage(response.getString("data"));

                                Utils.activity.refreshProfileData();

                                onBackPressed();
                            } else {
                                prgDialog.cancel();
                                Utils.psLog("Error : " + response );
                                showFailPopup();
                            }

                        } catch (JSONException e) {
                            prgDialog.cancel();
                            showFailPopup();
                            e.printStackTrace();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                prgDialog.cancel();
                VolleyLog.e("Error: ", error.getMessage());
            }
        });

        req.setShouldCache(false);
        // add the request object to the queue to be executed
        VolleySingleton.getInstance(this).addToRequestQueue(req);
    }

    private void showSuccessMessage(String success_status) {
        Toast.makeText(this, success_status, Toast.LENGTH_SHORT).show();
    }

    private void showFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.register);
        builder.setMessage(R.string.profile_edit_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    private Bitmap getResizedBitmap(Bitmap image, int maxSize) {
        int width = image.getWidth();
        int height = image.getHeight();

        float bitmapRatio = (float) width / (float) height;
        if (bitmapRatio > 0) {
            width = maxSize;
            height = (int) (width / bitmapRatio);
        } else {
            height = maxSize;
            width = (int) (height * bitmapRatio);
        }
        return Bitmap.createScaledBitmap(image, width, height, true);
    }

    private void uploadImage() {

        prgDialog.show();
        String url = Config.APP_API_URL + Config.POST_PROFILE_IMAGE;
        Utils.psLog("URL" + url);
        StringRequest stringRequest = new StringRequest(Request.Method.POST,
                url, new Response.Listener<String>() {

            @Override
            public void onResponse(String response) {
                try {
                    Utils.psLog("Server RESPONSE >> " + response);
                    JSONObject obj = new JSONObject(response);
                    String status = obj.getString("status");
                    if (status.equals(jsonStatusSuccessString)) {
                        String file_name = obj.getString("data");

                        Utils.psLog("success img upload to server");

                        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
                        SharedPreferences.Editor editor = prefs.edit();
                        editor.putString("_login_user_photo", file_name);
                        editor.apply();

                        ContextWrapper cw = new ContextWrapper(Utils.activity.getApplicationContext());
                        File directory = cw.getDir("imageDir", Context.MODE_APPEND);
                        File file = new File(directory, pref.getString("_login_user_photo", ""));

                        //File file = new File(Environment.getExternalStorageDirectory() + "/" + file_name);

                        FileOutputStream ostream = new FileOutputStream(file);
                        myImg.compress(Bitmap.CompressFormat.JPEG, 100, ostream);
                        ostream.close();

                        ivProfilePhoto.setImageBitmap(myImg);
                        prgDialog.cancel();
                        Toast.makeText(getBaseContext(),photoUploadSuccess
                                , Toast.LENGTH_SHORT)
                                .show();

                        Utils.activity.refreshProfileData();


                    } else {
                        Toast.makeText(getBaseContext(),
                                photoUploadNotSuccess, Toast.LENGTH_SHORT)
                                .show();
                        prgDialog.cancel();
                    }

                } catch (JSONException je) {
                    Utils.psErrorLog("JSON Exception" , je);
                    Toast.makeText(getBaseContext(),
                            "Error while loading data!",
                            Toast.LENGTH_LONG).show();
                    prgDialog.cancel();
                } catch (FileNotFoundException fe) {
                    Utils.psErrorLog("Error in File not found.", fe);
                    prgDialog.cancel();
                } catch (Exception e) {
                    Utils.psErrorLog("Error in Exception.", e);
                    prgDialog.cancel();
                }

            }

        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Utils.psLog("ERROR" + "Error [" + error + "]");
                Toast.makeText(getBaseContext(),
                        "Cannot connect to server", Toast.LENGTH_LONG)
                        .show();
                prgDialog.hide();
            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();

                params.put("image", encodedString);
                params.put("filename", fileName);
                params.put("userId", userId + "");
                params.put("platformName","android");
                return params;

            }

        };

        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                25000,
                DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        VolleySingleton.getInstance(this).addToRequestQueue(stringRequest);
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/
    public void doUpdate(View view) {

        if (inputValidation()) {

//            ProgressBar pb = (ProgressBar) findViewById(R.id.loading_spinner);
//            pb.setVisibility(View.VISIBLE);

            final String URL = Config.APP_API_URL + Config.PUT_USER_UPDATE + userId;
            Utils.psLog(URL);

            HashMap<String, String> params = new HashMap<>();
            params.put("username", etUserName.getText().toString().trim());
            params.put("email", etEmail.getText().toString().trim());
            params.put("about_me", etAboutMe.getText().toString().trim());
            params.put("phone", etUserPhone.getText().toString().trim());
            params.put("delivery_address", etUserDeliveryAddress.getText().toString().trim());
            params.put("billing_address", etUserBillingAddress.getText().toString().trim());
            params.put("platformName", "android");
            doSubmit(URL, params);

        }

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/


}
