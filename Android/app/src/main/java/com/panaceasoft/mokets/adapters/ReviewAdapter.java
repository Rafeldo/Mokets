package com.panaceasoft.mokets.adapters;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.models.PReviewData;
import com.panaceasoft.mokets.utilities.BitmapTransform;
import com.panaceasoft.mokets.utilities.Utils;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class ReviewAdapter extends BaseAdapter {

    private Activity activity;
    private LayoutInflater inflater;
    private ArrayList<PReviewData> reviewData;
    private Picasso p;

    public ReviewAdapter(Activity activity, ArrayList<PReviewData> reviewData, Picasso p) {
        this.activity = activity;
        this.reviewData = reviewData;
        this.p = p;
    }

    @Override
    public int getCount() {
        if(reviewData != null) {
            return reviewData.size();
        }
        return 0;
    }

    @Override
    public Object getItem(int position) {
        return reviewData.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (inflater == null)
            inflater = (LayoutInflater) activity
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        if (convertView == null)
            convertView = inflater.inflate(R.layout.review_row, null);

        TextView txtUserName = (TextView) convertView.findViewById(R.id.user_name);
        txtUserName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        TextView txtMessage = (TextView) convertView.findViewById(R.id.message);
        txtMessage.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        TextView txtAgo = (TextView) convertView.findViewById(R.id.ago);
        txtAgo.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        final ImageView imgUserPhoto = (ImageView) convertView.findViewById(R.id.thumbnail);

        PReviewData review = reviewData.get(position);

        txtUserName.setText(review.appuser_name);
        txtMessage.setText(review.review);
        txtAgo.setText(review.added);

        int MAX_WIDTH = Utils.getScreenWidth()/2;
        int MAX_HEIGHT = MAX_WIDTH;

        p.load(Config.APP_IMAGES_URL + review.profile_photo)
                .placeholder(R.drawable.ic_person_black)
                .transform(new BitmapTransform(MAX_WIDTH, MAX_HEIGHT))
                .into(imgUserPhoto);

        return convertView;
    }
}
