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
import android.widget.LinearLayout;
import android.widget.TextView;


import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.models.PTransactionData;
import com.panaceasoft.mokets.utilities.Utils;

import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */


public class TransactionAdapter extends RecyclerView.Adapter<TransactionAdapter.TransactionViewHolder>  {
    private Activity activity;
    public int lastPosition = -1;
    private List<PTransactionData> pTransactionDataList;

    public static class TransactionViewHolder extends RecyclerView.ViewHolder {

        LinearLayout llTransactionRow;
        TextView tvTransactionId;
        TextView tvTotalAmount;
        TextView tvStatus;


        TransactionViewHolder(View itemView) {
            super(itemView);
            llTransactionRow = (LinearLayout)itemView.findViewById(R.id.ll_transaction_row);
            tvTransactionId = (TextView)itemView.findViewById(R.id.tv_transaction_id);
            tvTotalAmount = (TextView)itemView.findViewById(R.id.tv_total_amount);
            tvStatus = (TextView)itemView.findViewById(R.id.tv_status);

            tvTransactionId.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvTotalAmount.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tvStatus.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        }
    }

    public TransactionAdapter(Context context, List<PTransactionData> cities){
        this.activity = (Activity) context;
        this.pTransactionDataList = cities;
    }

    @Override
    public void onAttachedToRecyclerView(RecyclerView recyclerView) {
        super.onAttachedToRecyclerView(recyclerView);
    }

    @Override
    public TransactionViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.transaction_row, parent, false);
        TransactionViewHolder svh = new TransactionViewHolder(v);
        return svh;
    }

    @TargetApi(Build.VERSION_CODES.HONEYCOMB_MR1)
    @Override
    public void onBindViewHolder(final TransactionViewHolder holder, int position) {
        final PTransactionData transactionData = pTransactionDataList.get(position);
        holder.tvTransactionId.setText(activity.getResources().getString(R.string.transaction_id) +" "+ String.valueOf(transactionData.id));
        holder.tvTotalAmount.setText(activity.getResources().getString(R.string.total_amount) +" " + String.valueOf(transactionData.total_amount) + transactionData.currency_symbol);
        holder.tvStatus.setText(activity.getResources().getString(R.string.transaction_status) + " " + transactionData.transaction_status);

        setAnimation(holder.llTransactionRow, position);

    }

    @Override
    public int getItemCount() {

        if(pTransactionDataList != null) {
            return pTransactionDataList.size();
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
