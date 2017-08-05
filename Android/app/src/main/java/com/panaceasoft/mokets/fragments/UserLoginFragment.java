package com.panaceasoft.mokets.fragments;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.ContextWrapper;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v4.app.Fragment;
import android.support.v7.app.AlertDialog;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;
import com.afollestad.materialdialogs.MaterialDialog;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.activities.MainActivity;
import com.panaceasoft.mokets.activities.UserForgotPasswordActivity;
import com.panaceasoft.mokets.activities.UserLoginActivity;
import com.panaceasoft.mokets.activities.UserRegisterActivity;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import com.squareup.picasso.LruCache;
import com.squareup.picasso.OkHttpDownloader;
import com.squareup.picasso.Picasso;
import com.squareup.picasso.Target;
import android.os.AsyncTask;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.HashMap;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class UserLoginFragment extends Fragment {

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private View view;
    private EditText txtEmail;
    private EditText txtPassword;
    private Button btnLogin;
    private Button btnForgot;
    private Button btnRegister;
    private MaterialDialog dialog;
    private ProgressDialog prgDialog;
    private String jsonStatusSuccessString;
    private LinearLayout mainLayout;

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.fragment_user_login, container, false);

        initData();

        initUI();

        return view;
    }

    @Override
    public void onDestroy() {
        try {
            Utils.unbindDrawables(mainLayout);
            //GlobalData.shopdata = null;
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
            jsonStatusSuccessString = getResources().getString(R.string.json_status_success);

        }catch(Exception e){
            Utils.psErrorLogE("Error in init data.", e);
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

            mainLayout = (LinearLayout) this.view.findViewById(R.id.main_layout);

            txtEmail = (EditText) this.view.findViewById(R.id.input_email);
            txtEmail.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtPassword = (EditText) this.view.findViewById(R.id.input_password);
            txtPassword.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            btnLogin = (Button) this.view.findViewById(R.id.button_login);
            btnLogin.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            btnForgot = (Button) this.view.findViewById(R.id.button_forgot);
            btnForgot.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            btnRegister = (Button) this.view.findViewById(R.id.button_register);
            btnRegister.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            btnLogin.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    doLogin();
                }
            });

            btnForgot.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    Utils.psLog("Forgot Click Here");
                    doForgot();
                }
            });

            btnRegister.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    doRegister();
                }
            });

            prgDialog = new ProgressDialog(getActivity());
            prgDialog.setMessage("Please wait...");
            prgDialog.setCancelable(false);
        }catch (Exception e){
            Utils.psErrorLogE("Error in Init UI." , e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    private void doLogin() {

        if(inputValidation()) {

            final String URL = Config.APP_API_URL + Config.POST_USER_LOGIN;
            Utils.psLog(URL);

            HashMap<String, String> params = new HashMap<>();
            params.put("email", txtEmail.getText().toString().trim());
            params.put("password",txtPassword.getText().toString().trim());

            doSubmit(URL, params, view);

        }

    }

    private void doForgot() {
        if(getActivity() instanceof MainActivity) {
            ((MainActivity) getActivity()).openFragment(R.id.nav_forgot);
        }else if(getActivity() instanceof UserLoginActivity) {
            startActivity(new Intent(getActivity(),UserForgotPasswordActivity.class));
        }
    }

    private void doRegister() {
        if(getActivity() instanceof MainActivity) {
            ((MainActivity) getActivity()).openFragment(R.id.nav_register);
        }else if(getActivity() instanceof UserLoginActivity) {
            startActivity(new Intent(getActivity(),UserRegisterActivity.class));
            //Intent intent = new Intent(getActivity(), UserRegisterActivity.class);
            //getActivity().start(intent, 0);
        }
    }

    private void doSubmit(String postURL, HashMap<String, String> params, final View view) {
        prgDialog.show();
        JsonObjectRequest req = new JsonObjectRequest(postURL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {

                            Utils.psLog(" .... Starting User Login Callback .... ");

                            String status = response.getString("status");
                            Utils.psLog("Response"+ response);
                            if (status.equals(jsonStatusSuccessString)) {

                                JSONObject dat = response.getJSONObject("data");
                                String user_id = dat.getString("id");
                                String user_name = dat.getString("username");
                                String email = dat.getString("email");
                                String about_me = dat.getString("about_me");
                                String is_banned = dat.getString("is_banned");
                                String user_profile_photo = dat.getString("profile_photo");
                                String user_phone = dat.getString("phone");
                                String user_delivery_address = dat.getString("delivery_address");
                                String user_billing_address = dat.getString("billing_address");

                                SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getActivity().getApplicationContext());
                                SharedPreferences.Editor editor = prefs.edit();
                                editor.putInt("_login_user_id", Integer.parseInt(user_id));
                                editor.putString("_login_user_name", user_name);
                                editor.putString("_login_user_email", email);
                                editor.putString("_login_user_about_me", about_me);
                                editor.putString("_login_user_photo", user_profile_photo);
                                editor.putString("_login_user_phone", user_phone);
                                editor.putString("_login_user_delivery_address", user_delivery_address);
                                editor.putString("_login_user_billing_address", user_billing_address);
                                editor.apply();

                                // Update Menu
                                Utils.activity.bindMenu();

                                Utils.psLog("User Profile Photo : " + user_profile_photo);
                                if(! user_profile_photo.equals("")){
                                    loadProfileImage(user_profile_photo);
                                    //prgDialog.cancel();
                                }else {
                                    prgDialog.cancel();
                                    if(getActivity() instanceof MainActivity) {
                                        ((MainActivity) getActivity()).refreshProfile();
                                    }

                                    if(getActivity() instanceof UserLoginActivity){
                                        getActivity().finish();
                                    }
                                }



                            } else {
                                Utils.psLog("Login Fail");
                                prgDialog.cancel();
                                showFailPopup();


                            }

                        } catch (JSONException e) {
                            prgDialog.cancel();
                            Utils.psLog("Login Fail : " + e.getMessage());
                            e.printStackTrace();
                            showFailPopup();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                prgDialog.cancel();
                Utils.psLog("Error: "+ error.getMessage());
            }
        });
        req.setShouldCache(false);
        // add the request object to the queue to be executed
        VolleySingleton.getInstance(getActivity()).addToRequestQueue(req);

    }

    private void loadProfileImage(String path) {

        if(!path.equals("")) {

            new downloadProfileImage().execute(Config.APP_IMAGES_URL + path, path);

        }
    }

    private boolean inputValidation() {

        if(txtEmail.getText().toString().equals("")) {
            Toast.makeText(getActivity().getApplicationContext(), R.string.email_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }

        if(txtPassword.getText().toString().equals("")) {
            Toast.makeText(getActivity().getApplicationContext(), R.string.password_validation_message,
                    Toast.LENGTH_LONG).show();
            return false;
        }

        return true;

    }

    private void showFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(getActivity(), R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.login);
        builder.setMessage(R.string.login_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/


    private class downloadProfileImage extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... params) {

            URL url ;
            try {
                url = new URL(params[0]);

                URLConnection conection = url.openConnection();
                conection.connect();

                // input stream to read file - with 8k buffer
                InputStream input = new BufferedInputStream(url.openStream(), 8192);

                File file;
                ContextWrapper cw = new ContextWrapper(getActivity().getApplicationContext());
                File directory = cw.getDir("imageDir", Context.MODE_APPEND);
                file = new File(directory, params[1]);

                OutputStream output = new FileOutputStream(file.getAbsolutePath());

                byte data[] = new byte[1024];

                int count;
                while ((count = input.read(data)) != -1) {

                    // writing data to file
                    output.write(data, 0, count);
                }



            }catch (Exception ee) {
                ee.printStackTrace();
            }
            return null;

        }

        @Override
        protected void onPostExecute(String result) {
            // After download finished the profile image
            // shutdown the Picasso threads
            Utils.activity.showDownPicasso();

            prgDialog.cancel();
            if(getActivity() instanceof UserLoginActivity){
                getActivity().finish();
            }else {
                Utils.activity.refreshProfile();
            }
        }

        @Override
        protected void onPreExecute() {}

        @Override
        protected void onProgressUpdate(Void... values) {}
    }

}
