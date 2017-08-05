package com.panaceasoft.mokets.fragments;


import android.content.Context;
import android.content.ContextWrapper;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.os.Environment;
import android.preference.PreferenceManager;
import android.support.v4.app.Fragment;
import android.support.v4.content.ContextCompat;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.utilities.Utils;
import java.io.File;
import java.io.FileOutputStream;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class ProfileFragment extends Fragment {

    /**
     * ------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     * *------------------------------------------------------------------------------------------------
     */

    private SharedPreferences pref;
    private ImageView imgProfilePhoto;
    private TextView tvUserName;
    private TextView tvEmail;
    private TextView tvAboutMe;

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment

        View view = inflater.inflate(R.layout.fragment_profile, container, false);

        initUI(view);

        bindData();

        return view;
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/
    private void initUI(View view) {
        imgProfilePhoto = (ImageView) view.findViewById(R.id.iv_profile_photo);
        tvUserName = (TextView) view.findViewById(R.id.tv_name);
        tvUserName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        tvEmail = (TextView) view.findViewById(R.id.tv_email);
        tvEmail.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        tvAboutMe = (TextView) view.findViewById(R.id.tv_about_me);
        tvAboutMe.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/
    public void bindData() {
        try {
            pref = PreferenceManager.getDefaultSharedPreferences(getActivity().getBaseContext());

            tvUserName.setText(pref.getString("_login_user_name", ""));
            tvEmail.setText(pref.getString("_login_user_email", ""));

            if (pref.getString("_login_user_about_me", "").equals("")) {
                tvAboutMe.setVisibility(View.GONE);
            } else {
                tvAboutMe.setVisibility(View.VISIBLE);
                tvAboutMe.setText(pref.getString("_login_user_about_me", ""));
            }

            File file = null;

            ContextWrapper cw = new ContextWrapper(Utils.activity.getApplicationContext());
            File directory = cw.getDir("imageDir", Context.MODE_PRIVATE);
            file = new File(directory, pref.getString("_login_user_photo", ""));
            //file = new File(Environment.getExternalStorageDirectory() + "/" + pref.getString("_login_user_photo", ""));
            if (file.exists()) {
                Utils.psLog("File is exist");
                Bitmap myBitmap = BitmapFactory.decodeFile(file.getAbsolutePath());

                imgProfilePhoto.setImageBitmap(myBitmap);
            } else {
                Drawable myDrawable = ContextCompat.getDrawable(getActivity(), R.drawable.ic_person_black);
                imgProfilePhoto.setImageDrawable(myDrawable);
            }

            Utils.psLog("Successfully loaded.");

        } catch (Exception e) {
            Utils.psErrorLogE("Error in bind data.", e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/
}
