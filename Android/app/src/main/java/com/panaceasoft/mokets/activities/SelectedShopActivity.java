package com.panaceasoft.mokets.activities;

import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.design.widget.CollapsingToolbarLayout;
import android.support.design.widget.CoordinatorLayout;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.StaggeredGridLayoutManager;
import android.support.v7.widget.Toolbar;
import android.text.TextPaint;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.adapters.CategoryAdapter;
import com.panaceasoft.mokets.listeners.ClickListener;
import com.panaceasoft.mokets.listeners.RecyclerTouchListener;
import com.panaceasoft.mokets.models.CategoryRowData;
import com.panaceasoft.mokets.models.PCategoryData;
import com.panaceasoft.mokets.models.PShopData;
import com.panaceasoft.mokets.models.PSubCategoryData;
import com.panaceasoft.mokets.utilities.BitmapTransform;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.Utils;
import com.squareup.picasso.Picasso;
import java.lang.reflect.Field;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class SelectedShopActivity extends AppCompatActivity {

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    private CollapsingToolbarLayout collapsingToolbar;
    private Toolbar toolbar;
    private ImageView detailImage;
    private RecyclerView mRecyclerView;
    private StaggeredGridLayoutManager mLayoutManager;
    private CategoryAdapter mAdapter;
    private List<CategoryRowData> myDataset = new ArrayList<>();
    private CategoryRowData info;
    private int selectedShopID;
    private PShopData pShop;
    private ArrayList<PCategoryData> categoryArrayList;
    private ArrayList<PSubCategoryData> subCategoryArrayList;
    private MenuItem menuItem;
    private int basketCount = 0;
    DBHandler db = new DBHandler(this);
    private Picasso p;
    private CoordinatorLayout mainLayout;

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_selected_city);

        initUI();

        initData();

        saveSelectedShopInfo(pShop);

        bindData();

        loadCategoryGrid();

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_news, menu);
        menuItem = menu.findItem(R.id.action_basket);
        pShop = GlobalData.shopdata;
        selectedShopID = pShop.id;
        basketCount = db.getBasketCountByShopId(selectedShopID);

        SharedPreferences pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());

        if (pref.getInt("_login_user_id", 0) != 0) {
            if (basketCount > 0) {
                menuItem.setIcon(Utils.buildCounterDrawable(basketCount, R.drawable.ic_shopping_cart_white, this));
            }
        }else {
            menuItem.setVisible(false);
        }
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        final  Intent intent;
        if (id == R.id.action_news) {
            Utils.psLog("Open News Activity");

            intent = new Intent(this, NewsListActivity.class);
            intent.putExtra("selected_shop_id", selectedShopID + "");
            startActivity(intent);
            return true;
        } else if(id == R.id.action_basket) {
            if (basketCount > 0) {
                intent = new Intent(getApplicationContext(), BasketActivity.class);
                intent.putExtra("selected_shop_id", selectedShopID);
                startActivityForResult(intent, 1);
                overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
            } else {
                showCartEmpty();
            }
            return true;

        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onBackPressed() {
        finish();
        overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
    }


    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if(requestCode == 1){

            if(resultCode == RESULT_OK){
                if(data.getStringExtra("close_activity").equals("YES")){
                    finish();
                }
                basketCount = db.getBasketCountByShopId(selectedShopID);

                if (basketCount > 0) {
                    menuItem.setIcon(Utils.buildCounterDrawable(basketCount, R.drawable.ic_shopping_cart_white, this));
                } else {
                    menuItem.setVisible(false);
                }
            }

        }

    }

    @Override
    protected void onDestroy() {
        try {
            Utils.psLog("Clearing Objects on Destroy");

            mRecyclerView.addOnItemTouchListener(null);
            collapsingToolbar = null;
            toolbar = null;
            detailImage.setImageResource(0);
            detailImage = null;
            mLayoutManager = null;
            myDataset.clear();
            mAdapter = null;
            myDataset = null;

            p.shutdown();
            Utils.unbindDrawables(mainLayout);

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
        initCollapsingToolbarLayout();
        mainLayout = (CoordinatorLayout) findViewById(R.id.coordinator_layout);
        p =new Picasso.Builder(this).build();
    }

    private void initCollapsingToolbarLayout(){
        try {
            collapsingToolbar = (CollapsingToolbarLayout) findViewById(R.id.collapsing_toolbar);
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initCollapsingToolbarLayout.", e);
        }
    }

    private void initToolbar() {
        try {
            toolbar = (Toolbar) findViewById(R.id.toolbar);
            setSupportActionBar(toolbar);
//            if(Utils.isAndroid_5_0()){
//                Utils.setMargins(toolbar, 0, -102, 0, 0);
//            }
            toolbar.setNavigationOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });
            if(getSupportActionBar() != null) {
                getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            }
            toolbar.setTitle("");
            toolbar.startAnimation(AnimationUtils.loadAnimation(this, R.anim.fade_in));
        } catch (Resources.NotFoundException e) {
            Utils.psErrorLogE("Error in initToolbar.", e);
        }

    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/
    private void initData() {
        try {
            detailImage = (ImageView) findViewById(R.id.detail_image);
            pShop = GlobalData.shopdata;
            selectedShopID = pShop.id;
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initData.", e);
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
            if(collapsingToolbar != null){
                collapsingToolbar.setTitle(Utils.getSpannableString(pShop.name));
                makeCollapsingToolbarLayoutLooksGood(collapsingToolbar);
            }
            int MAX_WIDTH = Utils.getScreenWidth();
            int MAX_HEIGHT = Utils.getScreenWidth();
            p.load(Config.APP_IMAGES_URL + pShop.cover_image_file)
                    .transform(new BitmapTransform(MAX_WIDTH, MAX_HEIGHT))
                    .into(detailImage);
        } catch (Exception e) {
            Utils.psErrorLogE("Error in bindData.", e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Bind Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/
    public void loadCategoryGrid() {
        try {
            mRecyclerView = (RecyclerView) findViewById(R.id.my_recycler_view);

            mRecyclerView.setHasFixedSize(true);

            mLayoutManager = new StaggeredGridLayoutManager(2, StaggeredGridLayoutManager.VERTICAL);
            mRecyclerView.setLayoutManager(mLayoutManager);

            mAdapter = new CategoryAdapter(myDataset, mRecyclerView, p);
            mRecyclerView.setAdapter(mAdapter);

            categoryArrayList = pShop.categories;
            for(PCategoryData cd : categoryArrayList) {
                subCategoryArrayList = cd.sub_categories;

                info = new CategoryRowData();
                info.setCatName(cd.name);
                info.setCatImage(cd.cover_image_file);
                myDataset.add(info);
            }
            if(myDataset != null ) {
                mAdapter.notifyItemInserted(myDataset.size());
            }

            mRecyclerView.addOnItemTouchListener(new RecyclerTouchListener(this, mRecyclerView, new ClickListener() {
                @Override
                public void onClick(View view, int position) {
                    onItemClicked(position);
                }

                @Override
                public void onLongClick(View view, int position) {

                }
            }));

            mRecyclerView.startAnimation(AnimationUtils.loadAnimation(this, R.anim.fade_in));
        } catch (Resources.NotFoundException e) {
            Utils.psErrorLogE("Error in loadCategoryGrid.", e);
        }
    }

    public void onItemClicked( int position){
        final Intent intent;
        intent = new Intent(this,SubCategoryActivity.class);
        intent.putExtra("selected_category_index", position );
        intent.putExtra("selected_shop_id", selectedShopID);
        startActivityForResult(intent, 1);
        overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/


    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/
    private void makeCollapsingToolbarLayoutLooksGood(CollapsingToolbarLayout collapsingToolbarLayout) {
        try {
            final Field field = collapsingToolbarLayout.getClass().getDeclaredField("mCollapsingTextHelper");
            field.setAccessible(true);

            final Object object = field.get(collapsingToolbarLayout);
            final Field tpf = object.getClass().getDeclaredField("mTextPaint");
            tpf.setAccessible(true);

            ((TextPaint) tpf.get(object)).setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            ((TextPaint) tpf.get(object)).setColor(getResources().getColor(R.color.colorAccent));
        } catch (Exception ignored) {
        }
    }

    private void saveSelectedShopInfo(PShopData ct) {
        try {
            SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(this);
            SharedPreferences.Editor editor = prefs.edit();
            editor.putInt("_id", ct.id);
            editor.putString("_name", ct.name);
            editor.putString("_cover_image", ct.cover_image_file);
            editor.putString("_address", ct.address);
            editor.putString("_shop_region_lat", ct.lat);
            editor.putString("_shop_region_lng", ct.lng);
            editor.commit();
        } catch (Exception e) {
            Utils.psErrorLogE("Error in saveSelectedShopInfo.", e);
        }
    }

    private void showCartEmpty() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.cart_empty);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

}
