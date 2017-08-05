package com.panaceasoft.mokets.adapters;

import android.content.Context;
import android.graphics.Color;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.models.AttributeRowData;
import com.panaceasoft.mokets.utilities.Utils;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class SpinAdapter extends ArrayAdapter<AttributeRowData> {

    private Context context;
    private AttributeRowData[] values;


    public SpinAdapter(Context context, int textViewResourceId, AttributeRowData[] values) {

        super(context, textViewResourceId, values);
        this.context = context;
        this.values = values;

    }

    public int getCount(){
        return values.length;
    }

    public AttributeRowData getItem(int position) {
        return values[position];
    }

    public long getItemId(int position){
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        TextView label = new TextView(context);
        label.setText(values[position].getName());

        label.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        //label.setTextSize(context.getResources().getDimension(R.dimen.spinner_caption));
        return label;
    }

    @Override
    public View getDropDownView(int position, View convertView,
                                ViewGroup parent) {
        TextView label = new TextView(context);
        if(position == 0) {
            label.setTextColor(Color.BLACK);
        }
        label.setText(values[position].getName());
        label.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        int space1 = Utils.dpToPx(8);
        label.setPadding(space1, space1, space1, space1);


        return label;
    }

}
