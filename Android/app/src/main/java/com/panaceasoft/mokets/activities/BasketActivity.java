package com.panaceasoft.mokets.activities;

import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.graphics.Color;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.design.widget.CoordinatorLayout;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.SpannableString;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ListView;
import android.widget.TextView;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.adapters.BasketAdapter;
import com.panaceasoft.mokets.models.BasketData;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.Utils;
import com.squareup.picasso.Picasso;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;


/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class BasketActivity extends AppCompatActivity {
    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private Toolbar toolbar;
    private ListView listView;
    private List<BasketData> basketDataSet;
    private BasketAdapter adapter;
    private SpannableString basketString;
    DBHandler db = new DBHandler(this);
    private CoordinatorLayout coordinatorLayout;
    private SharedPreferences pref;
    public static Double totalAmount = 0.0;
    public static String currencySymbol;
    private TextView snackTextView;
    private int selectedShopId;
    private Picasso p;


    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_basket);
        pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());

        initData();
        initUI();
        bindData();
        initSnackBar();
    }

    @Override
    public void onBackPressed() {
        Intent in = new Intent();
        in.putExtra("close_activity", "NO");
        setResult(RESULT_OK, in);
        finish();
        overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_basket, menu);
        //MenuItem menuItem = menu.findItem(R.id.action_payment_option);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.action_payment_option) {

            Utils.psLog(" Ready To Payment Option ");

            Intent intent = new Intent(getApplicationContext(), PaymentOptionActivity.class);
            intent.putExtra("selected_shop_id", selectedShopId);
            //startActivity(intent);
            startActivityForResult(intent, 1);
            overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);

            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if(requestCode == 1){
            if(resultCode == RESULT_OK){
                if(data.getStringExtra("close_activity").equals("YES")){
                    Intent in = new Intent();
                    in.putExtra("close_activity", "YES");
                    setResult(RESULT_OK, in);
                    finish();
                }

            }

        }

    }

    @Override
    public void onDestroy() {

        try {
            toolbar = null;
            p.shutdown();

            Utils.unbindDrawables(coordinatorLayout);
            coordinatorLayout = null;

            super.onDestroy();
        }catch (Exception e){
            super.onDestroy();
        }

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    private void initUI() {
        initToolbar();
        initList();

    }

    private void initSnackBar() {

        try {
            coordinatorLayout = (CoordinatorLayout) findViewById(R.id
                    .coordinator_layout);
            Snackbar snackbar = Snackbar
                    .make(coordinatorLayout, getResources().getString(R.string.total_amount) + totalAmount + currencySymbol, Snackbar.LENGTH_INDEFINITE);

            snackbar.show();

            View sbView = snackbar.getView();
            snackTextView = (TextView) sbView.findViewById(android.support.design.R.id.snackbar_text);
            snackTextView.setTextColor(Color.YELLOW);
            snackTextView.setGravity(Gravity.CENTER_HORIZONTAL);
        }catch (Exception e){
            Utils.psErrorLog("Error in init Snack Bar.", e);
        }

    }

    private void initToolbar() {
        try {
            toolbar = (Toolbar) findViewById(R.id.toolbar);
            toolbar.setTitle("");
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

    private void initList() {
        try {
            listView = (ListView) findViewById(R.id.basket_list);
            //listView.setOnItemClickListener(new ListClickHandler());
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initList.", e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void initData(){
        try {
            p = new Picasso.Builder(this).build();
            selectedShopId = getIntent().getIntExtra("selected_shop_id", 0);
            basketString = Utils.getSpannableString(getString(R.string.title_activity_basket));
        } catch (Resources.NotFoundException e) {
            Utils.psErrorLogE("Error in initToolbar.", e);
        }
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void bindData() {

        try {
            toolbar.setTitle(basketString);
            basketDataSet = new ArrayList<>();
            adapter = new BasketAdapter(this, basketDataSet, pref.getInt("_login_user_id", 0), db, selectedShopId, p);
            listView.setAdapter(adapter);


            loadBasketData();
        }catch (Exception e){
            Utils.psErrorLog("Error in bindData.", e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/


    private void loadBasketData() {
        try {
            if (basketDataSet != null) {
                basketDataSet.clear();
            }
            adapter.notifyDataSetChanged();
            List<BasketData> basket = db.getAllBasketDataByShopId(selectedShopId);

            totalAmount = 0.0;
            for (BasketData basketData : basket) {
                basketDataSet.add(basketData);
                currencySymbol = basketData.getCurrencySymbol();
                totalAmount += basketData.getQty() * Float.parseFloat(basketData.getUnitPrice());
            }

            totalAmount = Double.valueOf(String.format(Locale.US, "%.1f", totalAmount));
        }catch (Exception e){
            Utils.psErrorLog("Error in load basket data", e);
        }

    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    public void updateTotalAmount(Double updateAmount){
        try {
            updateAmount = Double.valueOf(String.format(Locale.US, "%.1f", updateAmount));
            Utils.psLog(" updateAmount >> " + updateAmount);
            snackTextView.setText("Total Amount : " + updateAmount + currencySymbol);
            totalAmount = updateAmount;
        }catch (Exception e){
            Utils.psErrorLog("Error in update total amount.", e);
        }
    }

}
