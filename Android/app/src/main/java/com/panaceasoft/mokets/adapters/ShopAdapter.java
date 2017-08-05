package com.panaceasoft.mokets.adapters;

import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.models.PShopData;
import com.panaceasoft.mokets.utilities.BitmapTransform;
import com.panaceasoft.mokets.utilities.Utils;
import com.squareup.picasso.Picasso;

import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */


public class ShopAdapter extends RecyclerView.Adapter<ShopAdapter.ShopViewHolder>  {
    private Activity activity;
    private int lastPosition = -1;
    private List<PShopData> pShopDataList;
    private Picasso p;

    public static class ShopViewHolder extends RecyclerView.ViewHolder {

        RelativeLayout cv;
        TextView shopName;
        ImageView shopPhoto;
        TextView shopDesc;


        ShopViewHolder(View itemView) {
            super(itemView);
            cv = (RelativeLayout)itemView.findViewById(R.id.shop_cv);
            shopName = (TextView)itemView.findViewById(R.id.city_name);
            shopDesc = (TextView)itemView.findViewById(R.id.city_desc);
            shopPhoto = (ImageView)itemView.findViewById(R.id.city_photo);
            shopName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            shopDesc.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        }
    }

    public ShopAdapter(Context context, List<PShopData> cities, Picasso p){
        this.activity = (Activity) context;
        this.pShopDataList = cities;
        this.p = p;
    }

    @Override
    public void onAttachedToRecyclerView(RecyclerView recyclerView) {
        super.onAttachedToRecyclerView(recyclerView);
    }

    @Override
    public ShopViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.shop_row_container, parent, false);
        ShopViewHolder svh = new ShopViewHolder(v);
        return svh;
    }

    int MAX_WIDTH = Utils.getScreenWidth();
    int MAX_HEIGHT = MAX_WIDTH;
    @TargetApi(Build.VERSION_CODES.HONEYCOMB_MR1)
    @Override
    public void onBindViewHolder(final ShopViewHolder holder, int position) {
        final PShopData city = pShopDataList.get(position);
        holder.shopName.setText(city.name);
        holder.shopDesc.setText(city.description.substring(0, Math.min(city.description.length(), 150)) + "...");

        p.load(Config.APP_IMAGES_URL + city.cover_image_file)
                .transform(new BitmapTransform(MAX_WIDTH, MAX_HEIGHT))
                .placeholder(R.drawable.ps_icon)
                .into(holder.shopPhoto);

        setAnimation(holder.cv, position);

        holder.itemView.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View view) {
//                final Intent intent;
//                intent = new Intent(holder.itemView.getContext(),SelectedShopActivity.class);
//                GlobalData.citydata = city;
//                intent.putExtra("selected_city_id", city.id);
//                holder.itemView.getContext().startActivity(intent);

            }
        });

    }

    @Override
    public int getItemCount() {
        if(pShopDataList != null) {
            return pShopDataList.size();
        }
        return 0;
    }

    private void setAnimation(View viewToAnimate, int position)
    {
        if (position > lastPosition)
        {
            Animation animation = AnimationUtils.loadAnimation(activity, R.anim.abc_slide_in_bottom);
            viewToAnimate.startAnimation(animation);
            lastPosition = position;
        }else{
            lastPosition = position;
        }
    }

}
