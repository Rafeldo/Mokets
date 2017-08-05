package com.panaceasoft.mokets.adapters;

import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Context;
import android.os.Build;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.models.PTransactionData;
import com.panaceasoft.mokets.models.PTransactionDetailsData;
import com.panaceasoft.mokets.utilities.Utils;
import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */


public class TransactionDetailAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder>  {
    private Activity activity;
    private List<PTransactionDetailsData> pTransactionDetailDataList;
    private PTransactionData pTransactionData;

    private static final int VIEW_ITEM = 1;
    private static final int VIEW_HEADER = 0;

    private class TransactionItemViewHolder extends RecyclerView.ViewHolder {

        LinearLayout llTransactionRow;
        TextView tvItemName;
        TextView tvPrice;
        TextView tvQty;


        private TransactionItemViewHolder(View itemView) {
            super(itemView);
            llTransactionRow = (LinearLayout)itemView.findViewById(R.id.ll_transaction_row);
            tvItemName = (TextView)itemView.findViewById(R.id.tv_transaction_id);
            tvPrice = (TextView)itemView.findViewById(R.id.tv_total_amount);
            tvQty = (TextView)itemView.findViewById(R.id.tv_status);
            tvItemName.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvPrice.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvQty.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        }
    }

    private class TransactionHeaderViewHolder extends RecyclerView.ViewHolder {

        LinearLayout llTransactionRow;
        TextView tvTransactionId;
        TextView tvTotalAmount;
        TextView tvStatus;
        TextView tvPhone;
        TextView tvEmail;
        TextView tvBillingAddress;
        TextView tvDeliveryAddress;


        private TransactionHeaderViewHolder(View itemView) {
            super(itemView);
            llTransactionRow = (LinearLayout)itemView.findViewById(R.id.ll_transaction_row);
            tvTransactionId = (TextView)itemView.findViewById(R.id.tv_transaction_id);
            tvTotalAmount = (TextView)itemView.findViewById(R.id.tv_total_amount);
            tvStatus = (TextView)itemView.findViewById(R.id.tv_status);
            tvPhone = (TextView)itemView.findViewById(R.id.tv_phone);
            tvEmail = (TextView)itemView.findViewById(R.id.tv_email);
            tvBillingAddress = (TextView)itemView.findViewById(R.id.tv_billing_address);
            tvDeliveryAddress = (TextView)itemView.findViewById(R.id.tv_deliver_address);
            tvTransactionId.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvTotalAmount.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvStatus.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvPhone.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvEmail.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvBillingAddress.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvDeliveryAddress.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        }
    }

    public TransactionDetailAdapter(Context context, List<PTransactionDetailsData> pTransactionDetailDataList, PTransactionData pTransactionData){
        this.activity = (Activity) context;
        this.pTransactionDetailDataList = pTransactionDetailDataList;
        this.pTransactionData = pTransactionData;
    }

    @Override
    public void onAttachedToRecyclerView(RecyclerView recyclerView) {
        super.onAttachedToRecyclerView(recyclerView);
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {


        if(viewType == VIEW_HEADER ) {
            View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.transaction_detail_row_header, parent, false);
            return new TransactionHeaderViewHolder(v);

        }else {
            View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.transaction_detail_row, parent, false);
            return new TransactionItemViewHolder(v);
        }

    }

    @TargetApi(Build.VERSION_CODES.HONEYCOMB_MR1)
    @Override
    public void onBindViewHolder(final RecyclerView.ViewHolder holder, int position) {



        if(holder instanceof TransactionHeaderViewHolder){

            TransactionHeaderViewHolder tHolder = (TransactionHeaderViewHolder) holder;

            tHolder.tvTransactionId.setText(activity.getResources().getString(R.string.transaction_id) +" "+ String.valueOf(pTransactionData.id));
            tHolder.tvTotalAmount.setText(activity.getResources().getString(R.string.total_amount) +" " + String.valueOf(pTransactionData.total_amount) + pTransactionData.currency_symbol);
            tHolder.tvStatus.setText(activity.getResources().getString(R.string.transaction_status) + " " + pTransactionData.transaction_status);
            tHolder.tvPhone.setText(activity.getResources().getString(R.string.transaction_phone) + " " + pTransactionData.phone);
            tHolder.tvEmail.setText(activity.getResources().getString(R.string.transaction_email) + " " + pTransactionData.email);
            tHolder.tvBillingAddress.setText(activity.getResources().getString(R.string.transaction_billing_address) + " " + pTransactionData.billing_address);
            tHolder.tvDeliveryAddress.setText(activity.getResources().getString(R.string.transaction_deliver_address) + " " + pTransactionData.delivery_address);

        }else{

            int cPosition = position  - 1;

            TransactionItemViewHolder tHolder = (TransactionItemViewHolder) holder;

            final PTransactionDetailsData transactionData = pTransactionDetailDataList.get(cPosition);
            tHolder.tvItemName.setText(activity.getResources().getString(R.string.transaction_item_name) +" "+ String.valueOf(transactionData.item_name));
            tHolder.tvPrice.setText(activity.getResources().getString(R.string.transaction_item_price) +" " + String.valueOf(transactionData.unit_price));
            tHolder.tvQty.setText(activity.getResources().getString(R.string.transaction_qty) + " " + transactionData.qty);

        }
    }

    @Override
    public int getItemViewType(int position) {
        return position == 0 ? VIEW_HEADER: VIEW_ITEM;
    }

    @Override
    public int getItemCount() {
        if(pTransactionDetailDataList != null) {
            return pTransactionDetailDataList.size() + 1;
        }
        return 0;
    }


}
