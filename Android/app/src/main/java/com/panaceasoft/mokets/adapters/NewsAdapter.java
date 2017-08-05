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
import com.panaceasoft.mokets.models.PNewsData;
import com.panaceasoft.mokets.utilities.BitmapTransform;
import com.panaceasoft.mokets.utilities.Utils;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class NewsAdapter extends BaseAdapter{

    private Activity activity;
    private LayoutInflater inflater;
    private ArrayList<PNewsData> newsData;
    private Picasso p;

    public NewsAdapter(Activity activity, ArrayList<PNewsData> newsData, Picasso p) {
        this.activity = activity;
        this.newsData = newsData;
        this.p = p;
    }

    @Override
    public int getCount() {
        if(newsData != null) {
            return newsData.size();
        }else {
            return 0;
        }
    }

    @Override
    public Object getItem(int position) {
        return newsData.get(position);
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
            convertView = inflater.inflate(R.layout.news_row, null);

        TextView txtNewsTitle = (TextView) convertView.findViewById(R.id.news_title);
        txtNewsTitle.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        TextView txtMessage = (TextView) convertView.findViewById(R.id.message);
        txtMessage.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        TextView txtAgo = (TextView) convertView.findViewById(R.id.ago);
        txtAgo.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

        final ImageView imgNewsPhoto = (ImageView) convertView.findViewById(R.id.thumbnail);

        PNewsData news = newsData.get(position);

        txtNewsTitle.setText(news.title);
        txtMessage.setText(news.description.substring(0, Math.min(news.description.length(), 120)) + "...");
        txtAgo.setText(news.added);

        int MAX_WIDTH = Utils.getScreenWidth()/2;
        int MAX_HEIGHT = MAX_WIDTH;
        if(news.images.get(0).path != null) {
            p.load(Config.APP_IMAGES_URL + news.images.get(0).path)
                    .transform(new BitmapTransform(MAX_WIDTH, MAX_HEIGHT))
                    .into(imgNewsPhoto);
        }
        return convertView;
    }
}
