package com.panaceasoft.mokets.activities;

import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Parcelable;
import android.preference.PreferenceManager;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.TabLayout;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.SpannableString;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.fragments.TabFragment;
import com.panaceasoft.mokets.models.PCategoryData;
import com.panaceasoft.mokets.models.PSubCategoryData;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.Utils;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class SubCategoryActivity extends AppCompatActivity {

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/
    private Toolbar toolbar;
    private ArrayList<? extends Parcelable> categoryArrayList = null;
    private int selectedCategoryIndex = 0;
    private int selectedShopID;
    private int C_FRAGMENTS_TO_KEEP_IN_MEMORY=0;
    private ViewPager viewPager;
    private ArrayList<PCategoryData> categoriesList;
    private ArrayList<PSubCategoryData> subCategoriesList;
    private SharedPreferences prefs;
    private MenuItem menuItem;
    private int basketCount = 0;
    private Adapter adapter;
    DBHandler db = new DBHandler(this);
    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tab);

        initData();

        initUI();
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
        getMenuInflater().inflate(R.menu.menu_tab, menu);


        int i = 0;

        for(PCategoryData cd : categoriesList) {
            if(cd != null) {
                menu.add(0, i, 0, cd.name);
                i++;
            }
        }

        menuItem = menu.findItem(R.id.action_basket);


        SharedPreferences pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());

        if (pref.getInt("_login_user_id", 0) != 0) {
            basketCount = db.getBasketCountByShopId(selectedShopID);

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

        if(id == R.id.action_basket) {
            final  Intent intent;
            intent = new Intent(getApplicationContext(), BasketActivity.class);
            intent.putExtra("selected_shop_id", selectedShopID);
            startActivityForResult(intent, 1);
            overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
            return true;
        }

        loadCategoryUI(id);
        return super.onOptionsItemSelected(item);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {

        if(requestCode == 1){

            if(resultCode == RESULT_OK){
                TabFragment fragment = (TabFragment) ((Adapter) viewPager.getAdapter()).getItem(viewPager.getCurrentItem());
                fragment.refershLikeAndReview(data.getIntExtra("selected_item_id",0), data.getStringExtra("like_count"), data.getStringExtra("review_count"));

                if(data.getStringExtra("close_activity").equals("YES")){
                    Intent in = new Intent();
                    in.putExtra("close_activity", "YES");
                    setResult(RESULT_OK, in);
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
            adapter.mFragments.clear();
            adapter = null;
            viewPager.destroyDrawingCache();
            viewPager.removeAllViews();

            viewPager = null;

            Utils.unbindDrawables(findViewById(R.id.drawer_layout));

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
        try {
            viewPager = (ViewPager) findViewById(R.id.viewpager);
            if (viewPager != null) {
                initViewPager(viewPager, subCategoriesList);
            }
            TabLayout tabLayout = (TabLayout) findViewById(R.id.tabs);
            tabLayout.setTabMode(TabLayout.MODE_SCROLLABLE);
            tabLayout.setupWithViewPager(viewPager);
            updateTabFonts(tabLayout);

            initToolbar();
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initUI.", e);
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
            toolbar.setTitle(Utils.getSpannableString(categoriesList.get(selectedCategoryIndex).name));
        } catch (Exception e) {
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
            categoriesList = GlobalData.shopdata.categories;
            selectedCategoryIndex = getIntent().getIntExtra("selected_category_index", 0);
            selectedShopID = getIntent().getIntExtra("selected_shop_id", 0);
            subCategoriesList = categoriesList.get(selectedCategoryIndex).sub_categories;
            selectedCategoryIndex = getIntent().getIntExtra("selected_category_index", 0);
        } catch (Exception e) {
            Utils.psErrorLogE("Error in initData.", e);
        }
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/
    private void updateTabFonts(TabLayout tabLayout){
        for(int i =0 ; i< tabLayout.getTabCount(); i++) {
            TextView tt = new TextView(this);
            tt.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
            tt.setTextColor(Color.WHITE);
            tt.setText(tabLayout.getTabAt(i).getText());
            tabLayout.getTabAt(i).setCustomView(tt);
        }
    }


    private void initViewPager(ViewPager viewPager, ArrayList<PSubCategoryData> subCategoryArrayList) {
        adapter = new Adapter(getSupportFragmentManager());

        if(subCategoryArrayList != null ) {
            C_FRAGMENTS_TO_KEEP_IN_MEMORY = subCategoryArrayList.size();
            for (PSubCategoryData scd : subCategoryArrayList) {

                adapter.addFragment(TabFragment.newInstance(scd, selectedShopID), Utils.getSpannableString(scd.name) + "");

            }

            viewPager.setOffscreenPageLimit(C_FRAGMENTS_TO_KEEP_IN_MEMORY);

        }
        viewPager.setAdapter(adapter);


    }

    private void loadCategoryUI(int id){
        Intent intent = new Intent(this,SubCategoryActivity.class);
        intent.putExtra("selected_category_index", id);
        intent.putExtra("selected_shop_id", selectedShopID);
        startActivity(intent);
        this.finish();
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    public void openActivity(int selected_item_id, int selected_shop_id){
        final Intent intent;
        intent = new Intent(this, DetailActivity.class);
        Utils.psLog("Selected Shop ID : " + selectedShopID);
        intent.putExtra("selected_item_id", selected_item_id);
        intent.putExtra("selected_shop_id", selectedShopID);
        startActivityForResult(intent, 1);
        overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Static Class
     **------------------------------------------------------------------------------------------------*/
    public class Adapter extends FragmentPagerAdapter {
        private final List<Fragment> mFragments = new ArrayList<>();
        private final List<String> mFragmentTitles = new ArrayList<>();
        public Adapter(FragmentManager fm) {
            super(fm);
        }
        public void addFragment(Fragment fragment, String title) {
            mFragments.add(fragment);
            mFragmentTitles.add(title);
        }

        @Override
        public Object instantiateItem(ViewGroup container, int position) {
            return super.instantiateItem(container, position);
        }

        @Override
        public Fragment getItem(int position) {
            return mFragments.get(position);
        }

        @Override
        public int getCount() {
            if(mFragments != null) {
                return mFragments.size();
            }else{
                return 0;
            }
        }

        @Override
        public SpannableString getPageTitle(int position) {
            return Utils.getSpannableString(mFragmentTitles.get(position));
        }

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Static Class
     **------------------------------------------------------------------------------------------------*/


}
