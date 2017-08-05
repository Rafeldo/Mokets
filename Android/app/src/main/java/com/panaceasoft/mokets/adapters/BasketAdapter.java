package com.panaceasoft.mokets.adapters;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListAdapter;
import android.widget.TextView;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.activities.BasketActivity;
import com.panaceasoft.mokets.models.BasketData;
import com.panaceasoft.mokets.utilities.BitmapTransform;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.Utils;
import com.squareup.picasso.Picasso;
import java.util.List;
import java.util.Locale;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class BasketAdapter extends BaseAdapter implements ListAdapter {

    private Activity activity;
    private LayoutInflater inflater;
    private List<BasketData> basketData;
    private Context mContext;
    private int itemQty = 0;
    private int loginUserId = 0;
    private DBHandler db;
    private Double totalAmount = 0.0;
    private int selectedShopId;
    private Picasso p;

    public BasketAdapter(Activity activity, List<BasketData> basketData, int loginUserId, DBHandler dbHandler, int shopId, Picasso p) {
        this.activity = activity;
        this.basketData = basketData;
        this.db =dbHandler;
        this.loginUserId = loginUserId;
        this.selectedShopId = shopId;
        mContext = this.activity.getApplicationContext();
        this.p = p;
    }

    @Override
    public int getCount() {
        if(basketData != null) {
            return basketData.size();
        }else {
            return 0;
        }
    }

    @Override
    public Object getItem(int position) {
        return basketData.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        try {

            final ViewHolder holder;
            if (inflater == null)
                inflater = (LayoutInflater) activity
                        .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            if (convertView == null)
                convertView = inflater.inflate(R.layout.basket_row, parent, false);

            holder = new ViewHolder();

            holder.txtItemTitle = (TextView) convertView.findViewById(R.id.item_title);
            holder.txtItemTitle.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));


            holder.txtItemPrice = (TextView) convertView.findViewById(R.id.item_price);
            holder.txtItemPrice.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            holder.txtItemSubTotal = (TextView) convertView.findViewById(R.id.item_sub_total);
            holder.txtItemSubTotal.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            holder.txtItemQty = (TextView) convertView.findViewById(R.id.item_qty);
            holder.txtItemQty.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));


            holder.btnDelete = (Button) convertView.findViewById(R.id.delete_btn);
            holder.btnDelete.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    db.deleteBasketByIds(basketData.get(position).getItemId(), basketData.get(position).getShopId());
                    basketData.remove(position);
                    notifyDataSetChanged();

                    refreshTotalAmount();

                }
            });


            holder.btnIncrease = (Button) convertView.findViewById(R.id.increase_btn);
            holder.btnIncrease.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    itemQty = db.getQTYByIds(basketData.get(position).getItemId(), basketData.get(position).getShopId()) + 1;

                    holder.txtItemQty.setText("QTY : " + " " + itemQty);
                    String tmpItemSubTotal = "Sub Total : " + String.valueOf(String.format(Locale.US, "%.1f", (Float.valueOf(basketData.get(position).getUnitPrice()) * itemQty)) + basketData.get(position).getCurrencySymbol());
                    holder.txtItemSubTotal.setText(tmpItemSubTotal);


                    db.updateBasketByIds(new BasketData(
                            basketData.get(position).getItemId(),
                            basketData.get(position).getShopId(),
                            loginUserId,
                            basketData.get(position).getName(),
                            basketData.get(position).getDesc(),
                            String.valueOf(basketData.get(position).getUnitPrice()),
                            basketData.get(position).getDiscountPercent(),
                            itemQty,
                            basketData.get(position).getImagePath(),
                            basketData.get(position).getCurrencySymbol(),
                            basketData.get(position).getCurrencyShortForm(),
                            basketData.get(position).getSelectedAttributeNames(),
                            basketData.get(position).getSelectedAttributeIds()
                    ), basketData.get(position).getItemId(), basketData.get(position).getShopId());

                    refreshTotalAmount();

                }
            });


            holder.btnDecrease = (Button) convertView.findViewById(R.id.decrease_btn);
            holder.btnDecrease.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (db.getQTYByIds(basketData.get(position).getItemId(), basketData.get(position).getShopId()) != 1) {

                        itemQty = db.getQTYByIds(basketData.get(position).getItemId(), basketData.get(position).getShopId()) - 1;

                        holder.txtItemQty.setText("QTY : " + " " + itemQty);
                        String tmpItemSubTotal = "Sub Total : " + String.valueOf(String.format(Locale.US, "%.1f", (Float.valueOf(basketData.get(position).getUnitPrice()) * itemQty)) + basketData.get(position).getCurrencySymbol());
                        holder.txtItemSubTotal.setText(tmpItemSubTotal);

                        db.updateBasketByIds(new BasketData(
                                basketData.get(position).getItemId(),
                                basketData.get(position).getShopId(),
                                loginUserId,
                                basketData.get(position).getName(),
                                basketData.get(position).getDesc(),
                                String.valueOf(basketData.get(position).getUnitPrice()),
                                basketData.get(position).getDiscountPercent(),
                                itemQty,
                                basketData.get(position).getImagePath(),
                                basketData.get(position).getCurrencySymbol(),
                                basketData.get(position).getCurrencyShortForm(),
                                basketData.get(position).getSelectedAttributeNames(),
                                basketData.get(position).getSelectedAttributeIds()
                        ), basketData.get(position).getItemId(), basketData.get(position).getShopId());

                        refreshTotalAmount();
                    }

                }
            });

            BasketData basket = basketData.get(position);

            holder.txtItemTitle.setText(basket.getName());
            holder.txtItemPrice.setText(this.mContext.getResources().getString(R.string.price) + " " + basket.getUnitPrice() + basket.getCurrencySymbol());

            double calcuatedSubTotal = Float.parseFloat(basket.getUnitPrice()) * basket.getQty();
            calcuatedSubTotal = Double.valueOf(String.format(Locale.US, "%.1f", calcuatedSubTotal));
            //Float itemPrice = Float.parseFloat(basket.getUnitPrice());
            holder.txtItemSubTotal.setText(this.mContext.getResources().getString(R.string.sub_total) + " " + calcuatedSubTotal + basket.getCurrencySymbol());

            holder.txtItemQty.setText(this.mContext.getResources().getString(R.string.qty) + " " + basket.getQty());
            itemQty = basket.getQty();

            final ImageView imgItemPhoto = (ImageView) convertView.findViewById(R.id.thumbnail);

            int MAX_WIDTH = Utils.getScreenWidth() / 2;
            int MAX_HEIGHT = Utils.getScreenWidth() / 2;
            if (basket.getImagePath() != null) {
                //Picasso.with(activity.getApplicationContext()).load(Config.APP_IMAGES_URL + basket.getImagePath()).into(imgNewsPhoto);
                p.load(Config.APP_IMAGES_URL + basket.getImagePath())
                        .transform(new BitmapTransform(MAX_WIDTH, MAX_HEIGHT))
                        .into(imgItemPhoto);
            }
        }catch (Exception e){
            Utils.psErrorLog("Error in convert view.", e);
        }
        return convertView;
    }

    private void refreshTotalAmount() {
        try {
            List<BasketData> basket = db.getAllBasketDataByShopId(selectedShopId);
            for (BasketData basketData : basket) {
                totalAmount += basketData.getQty() * Float.parseFloat(basketData.getUnitPrice());
            }

            ((BasketActivity) activity).updateTotalAmount(totalAmount);
            totalAmount = 0.0;
        }catch (Exception e){
            Utils.psErrorLog("Error refreshTotalAmount.",e);
        }
    }

    private static class ViewHolder {
        private TextView txtItemTitle;
        private TextView txtItemPrice;
        private TextView txtItemSubTotal;
        private TextView txtItemQty;
        private Button btnDelete;
        private Button btnIncrease;
        private Button btnDecrease;
    }

}
