package com.panaceasoft.mokets.utilities;

import android.content.Context;
import android.widget.ArrayAdapter;

import com.rey.material.widget.Spinner;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class MyActivity {

    private Context context;
    public MyActivity(Context context) {
        this.context = context;
    }

    public Spinner getSpinner() {
        return new Spinner(context);
    }

//    public ArrayAdapter getAdapter(int resId, String[] values) {
//        return new ArrayAdapter(context, resId, values);
//    }

}
