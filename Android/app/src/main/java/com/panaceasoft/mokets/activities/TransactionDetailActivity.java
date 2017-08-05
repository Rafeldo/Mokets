package com.panaceasoft.mokets.activities;

import android.support.design.widget.CoordinatorLayout;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.widget.RelativeLayout;

import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.adapters.TransactionAdapter;
import com.panaceasoft.mokets.adapters.TransactionDetailAdapter;
import com.panaceasoft.mokets.models.PTransactionData;
import com.panaceasoft.mokets.models.PTransactionDetailsData;
import com.panaceasoft.mokets.utilities.Utils;

import java.util.ArrayList;
import java.util.List;

public class TransactionDetailActivity extends AppCompatActivity {

    private Toolbar toolbar ;
    private RelativeLayout rlNoData;
    //private SwipeRefreshLayout swipeRefreshLayout;
    private RecyclerView recyclerView;
    private TransactionDetailAdapter adapter;
    private List<PTransactionDetailsData> pTransactionDetailsDatas;
    private int transactionID = 0;
    private int selectedPosition = 0;
    private PTransactionData pTransactionData = null;
    private CoordinatorLayout mainLayout;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_transaction_detail);

        initData();

        initUI();

        bindData();
    }

    @Override
    public void onDestroy() {
        try {
            toolbar = null;
            Utils.unbindDrawables(mainLayout);
            mainLayout = null;

            super.onDestroy();
        }catch (Exception e){
            super.onDestroy();
        }
    }

    private void initData() {

        selectedPosition = getIntent().getIntExtra("selected_position", 0);

        Utils.psLog("Selected Position : " + selectedPosition);

        transactionID = getIntent().getIntExtra("selected_transaction_id", 0);

        Utils.psLog("Transaction id : " + transactionID);

        try {

            pTransactionData = GlobalData.transactionDatas.get(selectedPosition);

            Utils.psLog("Transaction No : " + pTransactionData.id);

        }catch (Exception e){
            Utils.psErrorLog("Error in getting transaction data." , e);
        }

        try {
            Utils.psLog("Transaction Detail Size : " + pTransactionData.details.size());
        }catch (Exception e){

            Utils.psErrorLogE("Error in transaction detail size.", e);

        }
    }

    private void initUI() {
        initToolbar();

        mainLayout = (CoordinatorLayout) findViewById(R.id.activity_transaction_detail);

        rlNoData = (RelativeLayout) findViewById(R.id.rl_no_data);
        //swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swipe_refresh);
        recyclerView = (RecyclerView) findViewById(R.id.recycler_view);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        pTransactionDetailsDatas = new ArrayList<>();


    }

    private void initToolbar() {
        try {
            toolbar = (Toolbar) findViewById(R.id.toolbar);
            setSupportActionBar(toolbar);
            toolbar.setNavigationOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();

                }
            });
            if(getSupportActionBar() != null) {
                getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            }
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initToolbar.", e);
        }

    }

    private void bindData() {
        pTransactionDetailsDatas.clear();

        if(pTransactionData != null){
            if(pTransactionData.details != null && pTransactionData.details.size() > 0 ) {
                rlNoData.setVisibility(View.GONE);
                Utils.psLog("Detail List.");
                pTransactionDetailsDatas = pTransactionData.details;
            }
        }

        //adapter.notifyItemInserted(pTransactionDetailsDatas.size());

        adapter = new TransactionDetailAdapter(this, pTransactionDetailsDatas, pTransactionData);
        recyclerView.setAdapter(adapter);

        recyclerView.getAdapter().notifyDataSetChanged();
    }
}
