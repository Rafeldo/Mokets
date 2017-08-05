package com.panaceasoft.mokets.activities;

import android.animation.Animator;
import android.animation.AnimatorSet;
import android.animation.ObjectAnimator;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.design.widget.CoordinatorLayout;
import android.support.design.widget.FloatingActionButton;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.view.animation.BounceInterpolator;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RatingBar;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.JsonObjectRequest;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdView;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.GlobalData;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.adapters.SpinAdapter;
import com.panaceasoft.mokets.models.AttributeRowData;
import com.panaceasoft.mokets.models.BasketData;
import com.panaceasoft.mokets.models.PAttributesData;
import com.panaceasoft.mokets.models.PItemData;
import com.panaceasoft.mokets.models.PReviewData;
import com.panaceasoft.mokets.models.PShopData;
import com.panaceasoft.mokets.utilities.DBHandler;
import com.panaceasoft.mokets.utilities.MyActivity;
import com.panaceasoft.mokets.utilities.Utils;
import com.panaceasoft.mokets.utilities.VolleySingleton;
import com.panaceasoft.mokets.utilities.BitmapTransform;
import com.rey.material.widget.Spinner;
import com.squareup.picasso.LruCache;
import com.squareup.picasso.Picasso;
import org.json.JSONException;
import org.json.JSONObject;
import org.w3c.dom.Text;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;

import android.support.v7.app.AlertDialog;


/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class DetailActivity extends AppCompatActivity {

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Private Variables
    //-------------------------------------------------------------------------------------------------------------------------------------
    private Toolbar toolbar;
    private ImageView detailImage;
    private SharedPreferences pref;
    private TextView txtLikeCount;
    private TextView txtReviewCount;
    private TextView txtTotalReview;
    private TextView txtReviewMessage;
    private TextView txtNameTime;
    private TextView txtPhone;
    private TextView txtDescription;
    private TextView title;
    private TextView txtPrice;
    private TextView txtQty;
    private EditText editTextQty;
    private TextView txtDiscount;
    private ImageView userPhoto;
    private Button btnLike;
    private Button btnMoreReview;
    private Button btnInquiry;
    private FloatingActionButton fab;
    private int selectedItemId;
    private int selectedShopId;
    private Bundle bundle;
    private Intent intent;
    private Boolean isFavourite = false;
    private RatingBar getRatingBar;
    private RatingBar setRatingBar;
    private TextView ratingCount;
    private Animation animation;
    private String jsonStatusSuccessString;
    private LinearLayout attributeTitleLayout;
    DBHandler db = new DBHandler(this);
    private String selectedAttributeName = "";
    private String selectedAttributeIds = "";
    private double calcuatedPrice = 0.0;
    private double additionalPrice = 0.0;
    private int basketCount = 0;
    private MenuItem menuItem;
    private float touchX = 0 ;
    private float touchY = 0 ;
    private ImageView ivAndriod;
    private Picasso p;
    private CoordinatorLayout mainLayout;
    private double attributePriceOnly = 0.0;
    private TextView txtSelectedAttribtue;
    private LinearLayout attributeSelectedLayout;


    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Private Variables
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Override Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_detail);

        initData();

        initUI();

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == 1) {
            if (resultCode == RESULT_OK) {

                bindTitle();
                bindDescription();
                bindToolbarImage();
                bindCountValues();
                bindReview();
                bindRate();
                bindFavourite(fab);
                bindPrice();
                bindQty();
                bindDiscount();
                bindCart();

                try{
                    if (data.getStringExtra("close_activity").equals("YES")) {
                        Intent in = new Intent();
                        in.putExtra("close_activity", "YES");
                        setResult(RESULT_OK, in);
                        finish();
                    }
                }catch (Exception e){
                    Utils.psLog("No data in data for close activity checking.");
                }



            }
        }
    }

    @Override
    public void onBackPressed() {
        try {
            Intent in = new Intent();
            in.putExtra("selected_item_id", GlobalData.itemData.id);
            in.putExtra("like_count", GlobalData.itemData.like_count);
            in.putExtra("review_count", GlobalData.itemData.review_count);
            in.putExtra("close_activity", "NO");
            setResult(RESULT_OK, in);

            GlobalData.itemData = null;
            finish();
            overridePendingTransition(R.anim.blank_anim, R.anim.left_to_right);
        }catch (Exception e) {
            Utils.psErrorLogE("Error in BackPress.", e);
            finish();

        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_cart, menu);
        menuItem = menu.findItem(R.id.action_basket);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.action_basket) {

            if (pref.getInt("_login_user_id", 0) != 0) {
                basketCount = db.getBasketCountByShopId(GlobalData.itemData.shop_id);
                if (basketCount > 0) {
                    intent = new Intent(getApplicationContext(), BasketActivity.class);
                    intent.putExtra("selected_shop_id", selectedShopId);


                        if(GlobalData.shopDatas != null){

                            for(PShopData pShopData : GlobalData.shopDatas){
                                if(pShopData.id == GlobalData.itemData.shop_id){
                                    GlobalData.shopdata = pShopData;
                                    startActivityForResult(intent, 1);
                                    overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
                                    break;
                                }
                            }

                        }else {
                            Toast.makeText(this, "Can't find shop data.", Toast.LENGTH_SHORT).show();
                        }



                } else {
                    showCartEmpty();
                }
            }
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onDestroy() {
        try {
            toolbar = null;
            detailImage = null;
            pref = null;
            txtLikeCount = null;
            txtReviewCount = null;
            txtTotalReview = null;
            txtReviewMessage = null;
            txtNameTime = null;
            txtPhone = null;
            txtDescription = null;
            title = null;
            userPhoto = null;
            btnLike = null;
            btnMoreReview = null;
            btnInquiry = null;
            fab = null;
            bundle = null;
            intent = null;
            getRatingBar = null;
            setRatingBar = null;
            ratingCount = null;
            animation = null;

            p.shutdown();

            Utils.unbindDrawables(mainLayout);
            mainLayout = null;
            GlobalData.itemData = null;

            super.onDestroy();
        }catch (Exception e){
            super.onDestroy();
        }
    }

    @Override
    public boolean dispatchTouchEvent(MotionEvent event) {

        // Event to know the user clicked point
        this.touchX = event.getX();
        this.touchY = event.getY();

        return super.dispatchTouchEvent(event);
    }



    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Override Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Init Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void initData() {

        p =new Picasso.Builder(this)
                .memoryCache(new LruCache(1))
                .build();

        pref = PreferenceManager.getDefaultSharedPreferences(getBaseContext());

        selectedItemId = getIntent().getIntExtra("selected_item_id", 0);
        selectedShopId = getIntent().getIntExtra("selected_shop_id", 0);

        requestData(Config.APP_API_URL + Config.ITEMS_BY_ID + selectedItemId + "/shop_id/" + selectedShopId);
        jsonStatusSuccessString = getResources().getString(R.string.json_status_success);

        updateTouchCount(selectedItemId);
    }

    private void updateTouchCount(int selectedItemId) {
        try {
            final String URL = Config.APP_API_URL + Config.POST_TOUCH_COUNT + selectedItemId;
            HashMap<String, String> params = new HashMap<>();
            params.put("appuser_id", String.valueOf(pref.getInt("_login_user_id", 0)));
            params.put("shop_id", String.valueOf(pref.getInt("_id", 0)));
            doSubmit(URL, params, "touch");
        }catch (Exception e){
            Utils.psErrorLogE("Error in Touch Count.", e);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion init Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // init UI Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void initUI() {

        try {
            initToolbar();

            btnLike = (Button) findViewById(R.id.btn_like);
            btnLike.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtLikeCount = (TextView) findViewById(R.id.total_like_count);
            txtLikeCount.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtReviewCount = (TextView) findViewById(R.id.total_review_count);
            txtReviewCount.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtTotalReview = (TextView) findViewById(R.id.total_review);
            txtTotalReview.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtReviewMessage = (TextView) findViewById(R.id.review_message);
            txtReviewMessage.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtNameTime = (TextView) findViewById(R.id.name_time);
            txtNameTime.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            TextView txtAvailableAttribute = (TextView) findViewById(R.id.available_attribute);
            txtAvailableAttribute.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtPrice = (TextView) findViewById(R.id.price);
            txtPrice.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtQty = (TextView) findViewById(R.id.qty);
            txtQty.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtDescription = (TextView) findViewById(R.id.txtDescription);
            txtDescription.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            title = (TextView) findViewById(R.id.title);
            title.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            txtDiscount = (TextView) findViewById(R.id.discount);
            txtDiscount.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            editTextQty = (EditText) findViewById(R.id.edit_text_Qty);
            editTextQty.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            userPhoto = (ImageView) findViewById(R.id.user_photo);
            detailImage = (ImageView) findViewById(R.id.detail_image);

            btnMoreReview = (Button) findViewById(R.id.btn_more_review);
            btnMoreReview.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            btnInquiry = (Button) findViewById(R.id.btn_inquiry);
            btnInquiry.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            Button btnShopInfo = (Button) findViewById(R.id.btn_shopinfo);
            btnShopInfo.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            Button btnAddToCart = (Button) findViewById(R.id.btn_add_to_cart);
            btnAddToCart.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            getRatingBar = (RatingBar) findViewById(R.id.get_rating);
            setRatingBar = (RatingBar) findViewById(R.id.set_rating);
            ratingCount = (TextView) findViewById(R.id.rating_count);
            animation = AnimationUtils.loadAnimation(getBaseContext(), R.anim.pop_out);

            ivAndriod = (ImageView) findViewById(R.id.iv_android);

            attributeTitleLayout = (LinearLayout) findViewById(R.id.attribute_title);

            txtSelectedAttribtue = (TextView) findViewById(R.id.selected_attribute);
            txtSelectedAttribtue.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));

            attributeSelectedLayout = (LinearLayout) findViewById(R.id.selected_attribute_title);

            btnAddToCart.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    if (pref.getInt("_login_user_id", 0) != 0) {

                        if(editTextQty.getText().toString().matches("") || Integer.parseInt(editTextQty.getText().toString())== 0) {

                            showRequiredQty();

                        } else {

                            AnimatorSet animSetXYS = new AnimatorSet();
                            ObjectAnimator moveAnimS = ObjectAnimator.ofFloat(ivAndriod, "X", touchX);
                            ObjectAnimator moveAnimS2 = ObjectAnimator.ofFloat(ivAndriod, "Y", touchY - 50); // 50 will be height of animator object
                            animSetXYS.playTogether(moveAnimS, moveAnimS2);
                            animSetXYS.setDuration(1);
                            animSetXYS.addListener(new Animator.AnimatorListener() {
                                @Override
                                public void onAnimationStart(Animator animation) {

                                }

                                @Override
                                public void onAnimationEnd(Animator animation) {
                                    ivAndriod.setVisibility(View.VISIBLE);


                                    AnimatorSet animSetXY = new AnimatorSet();
                                    ObjectAnimator moveAnim = ObjectAnimator.ofFloat(ivAndriod, "X", Utils.getScreenWidth() - 50); // 50 will be width of animator
                                    ObjectAnimator moveAnim2 = ObjectAnimator.ofFloat(ivAndriod, "Y", 0);
                                    animSetXY.playTogether(moveAnim, moveAnim2);
                                    animSetXY.setDuration(800);
                                    animSetXY.addListener(new Animator.AnimatorListener() {
                                        @Override
                                        public void onAnimationStart(Animator animation) {

                                        }

                                        @Override
                                        public void onAnimationEnd(Animator animation) {
                                            ivAndriod.setVisibility(View.INVISIBLE);
                                            doAddToCart();
                                        }

                                        @Override
                                        public void onAnimationCancel(Animator animation) {

                                        }

                                        @Override
                                        public void onAnimationRepeat(Animator animation) {

                                        }
                                    });
                                    animSetXY.start();

                                }

                                @Override
                                public void onAnimationCancel(Animator animation) {

                                }

                                @Override
                                public void onAnimationRepeat(Animator animation) {

                                }
                            });
                            animSetXYS.start();
                        }

                    } else {
                        showNeedLogin();
                    }

                }
            });

            fab = (FloatingActionButton) findViewById(R.id.fab);

            fab.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    doFavourite(v);



                    animation.setAnimationListener(new Animation.AnimationListener() {
                        @Override
                        public void onAnimationStart(Animation animation) {

                            if (isFavourite) {
                                isFavourite = false;
                                fab.setImageResource(R.drawable.ic_favorite_border);
                            } else {
                                isFavourite = true;
                                fab.setImageResource(R.drawable.ic_favorite_white);
                            }
                        }

                        @Override
                        public void onAnimationEnd(Animation animation) {

                        }


                        @Override
                        public void onAnimationRepeat(Animation animation) {

                        }
                    });
                    fab.clearAnimation();
                    fab.startAnimation(animation);
                }
            });

            btnLike.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    doLike(v);

                    Animation rotate = AnimationUtils.loadAnimation(getBaseContext(), R.anim.fade_in);
                    btnLike.startAnimation(rotate);
                    rotate.setAnimationListener(new Animation.AnimationListener() {
                        @Override
                        public void onAnimationStart(Animation animation) {
                        }

                        @Override
                        public void onAnimationEnd(Animation animation) {
                        }

                        @Override
                        public void onAnimationRepeat(Animation animation) {
                        }
                    });
                }
            });

            editTextQty.setOnTouchListener(new View.OnTouchListener() {

                @Override
                public boolean onTouch(View v, MotionEvent event) {

                    v.setFocusable(true);
                    v.setFocusableInTouchMode(true);
                    return false;
                }
            });

            getRatingBar.setOnRatingBarChangeListener(new RatingBar.OnRatingBarChangeListener() {
                @Override
                public void onRatingChanged(RatingBar ratingBar, float rating, boolean fromUser) {
                    if (pref.getInt("_login_user_id", 0) != 0) {
                        ratingChanged( rating);
                    } else {
                        Toast.makeText(getApplicationContext(), R.string.login_required,
                                Toast.LENGTH_LONG).show();
                        getRatingBar.setRating(0);
                    }
                }
            });

            if(Config.SHOW_ADMOB) {
                AdView mAdView = (AdView) findViewById(R.id.adView);
                AdRequest adRequest = new AdRequest.Builder().build();
                mAdView.loadAd(adRequest);

                AdView mAdView2 = (AdView) findViewById(R.id.adView2);
                AdRequest adRequest2 = new AdRequest.Builder().build();
                mAdView2.loadAd(adRequest2);
            }else{
                AdView mAdView = (AdView) findViewById(R.id.adView);
                mAdView.setVisibility(View.GONE);
                AdView mAdView2 = (AdView) findViewById(R.id.adView2);
                mAdView2.setVisibility(View.GONE);
            }

        }catch(Exception e){
            Utils.psErrorLogE("Error in Init UI.", e);
        }

    }

    private void initToolbar() {
        toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (Utils.isAndroid_5_0()) {
            Utils.setMargins(toolbar, 0, -78, 0, 0);
        }
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });

        if(getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }
    }


    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion init UI Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Bind Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void bindData() {

        bindTitle();
        bindDescription();
        bindToolbarImage();
        bindCountValues();
        bindReview();
        bindRate();
        bindFavourite(fab);
        bindPrice();
        bindQty();
        bindAttribute();
        bindDiscount();
        bindCart();
        bindSelectedAttributeName();

    }

    private void bindSelectedAttributeName() {
        if(isBasketItem() > 0) {
            BasketData basket = db.getBasketById(GlobalData.itemData.id);
            txtSelectedAttribtue.setText(getString(R.string.selected_attribute) + " : " + Utils.removeLastChar(basket.getSelectedAttributeNames()));
        } else {
            attributeSelectedLayout.setVisibility(View.GONE);
        }
    }

    private void bindCart() {
        if (pref.getInt("_login_user_id", 0) != 0) {
            basketCount = db.getBasketCountByShopId(GlobalData.itemData.shop_id);
            if (basketCount > 0) {
                menuItem.setIcon(Utils.buildCounterDrawable(basketCount, R.drawable.ic_shopping_cart_white, this));
            } else {
                menuItem.setVisible(false);
            }
        }
    }

    private void bindTitle() {

        title.setTypeface(Utils.getTypeFace(Utils.Fonts.ROBOTO));
        title.setText(GlobalData.itemData.name);

    }

    private void bindPrice() {
        txtPrice.setText(getString(R.string.price) + GlobalData.itemData.unit_price + GlobalData.itemData.currency_symbol + "(" + GlobalData.itemData.currency_short_form + ")");
        calcuatedPrice = Double.valueOf(String.format(Locale.US, "%.1f", GlobalData.itemData.unit_price));

    }

    private void bindQty() {
        txtQty.setText(getString(R.string.qty));
        if(isBasketItem() > 0) {
            BasketData basket = db.getBasketById(GlobalData.itemData.id);

            editTextQty.setText(String.valueOf(basket.getQty()));
            editTextQty.setBackgroundColor(ContextCompat.getColor(this, R.color.colorAccent));
            editTextQty.setTextColor(ContextCompat.getColor(this, R.color.textColorPrimary));

        }


    }

    int MAX_WIDTH = Utils.getScreenWidth();
    int MAX_HEIGHT = Utils.getScreenWidth();

    private void bindToolbarImage() {
        try {
            detailImage.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    openGallery();
                }
            });
            p.load(Config.APP_IMAGES_URL + GlobalData.itemData.images.get(0).path)
                    .transform(new BitmapTransform(MAX_WIDTH, MAX_HEIGHT))
                    .into(detailImage);

        }catch(Exception e){
            Utils.psErrorLogE("Error in Bind Toolbar Image.", e);
        }
    }

    private void bindCountValues() {
        try {
            txtLikeCount.setText(" " + GlobalData.itemData.like_count + " ");
            txtReviewCount.setText(" " + GlobalData.itemData.review_count + " ");
        }catch(Exception e){
            Utils.psErrorLogE("Error in Bind Count.", e);
        }
    }

    private void bindDiscount() {

        if(GlobalData.itemData.discount_type_id == 0) {
            txtDiscount.setVisibility(View.GONE);
        } else {
            txtDiscount.setText("(" + GlobalData.itemData.discount_name + ")");
            calcuatedPrice =  GlobalData.itemData.unit_price - (Float.parseFloat(GlobalData.itemData.discount_percent) * GlobalData.itemData.unit_price);
            calcuatedPrice = Double.valueOf(String.format(Locale.US, "%.1f", calcuatedPrice));

            txtPrice.setText(getString(R.string.price) +
                    calcuatedPrice + GlobalData.itemData.currency_symbol + "(" + GlobalData.itemData.currency_short_form + ")");

        }

    }

    private void bindReview() {
        try {
            ArrayList<PReviewData> itemReviewData = GlobalData.itemData.reviews;


            txtNameTime.setVisibility(View.VISIBLE);
            txtReviewMessage.setVisibility(View.VISIBLE);
            btnMoreReview.setText(getString(R.string.view_more_review));

            if(itemReviewData != null) {
                if (itemReviewData.size() > 0) {
                    if (itemReviewData.size() == 1) {
                        txtTotalReview.setText(itemReviewData.size() + " " + getString(R.string.review));
                    } else {
                        txtTotalReview.setText(itemReviewData.size() + " " + getString(R.string.reviews));
                    }


                    try {
                        PReviewData reviewData = itemReviewData.get(0);
                        txtNameTime.setText(reviewData.appuser_name + " " + "(" + reviewData.added + ")");
                        txtReviewMessage.setText(reviewData.review);
                        if (!reviewData.profile_photo.equals("")) {
                            Utils.psLog(" Loading User photo : " + Config.APP_IMAGES_URL + reviewData.profile_photo);
                            p.load(Config.APP_IMAGES_URL + reviewData.profile_photo).resize(150, 150).into(userPhoto);
                        } else {
                            userPhoto.setColorFilter(Color.argb(114, 114, 114, 114));
                        }
                    } catch (Exception e) {
                        Utils.psErrorLog("Error in review UI binding.", e);
                    }


                } else {
                    txtTotalReview.setText(getString(R.string.no_review_count));
                    txtNameTime.setVisibility(View.GONE);
                    txtReviewMessage.setVisibility(View.GONE);
                    btnMoreReview.setText(getString(R.string.add_first_review));

                    Drawable myDrawable = ContextCompat.getDrawable(this, R.drawable.ic_rate_review_black);
                    userPhoto.setImageDrawable(myDrawable);

                }
            }
        }catch (Exception e){
            Utils.psErrorLogE("Error in Bind Reviews.", e);
        }
    }

    private void bindAttribute() {

        LinearLayout attributeContainer = (LinearLayout) findViewById(R.id.attribute_container);
        MyActivity myAct = new MyActivity(this);

        ArrayList<PAttributesData> itemAttributeData = GlobalData.itemData.attributes;

        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        Utils.psLog("Param Left " + Utils.dpToPx(44));
        params.setMargins(Utils.dpToPx(44), 5, 10, 10);

        if(itemAttributeData != null) {
            if (itemAttributeData.size() > 0) {

                for (int i = 0; i < itemAttributeData.size(); i++) {
                    Spinner spin = myAct.getSpinner();
                    spin.setMinimumHeight(Utils.dpToPx(24));

                    if(itemAttributeData.get(i) != null) {
                        if(itemAttributeData.get(i).details != null) {

                            AttributeRowData[] attributesData = new AttributeRowData[itemAttributeData.get(i).details.size() + 1];

                            for (int j = 0; j < itemAttributeData.get(i).details.size() + 1; j++) {
                                attributesData[j] = new AttributeRowData();
                                if (j == 0) {
                                    attributesData[j].setId(0);
                                    attributesData[j].setShopId(0);
                                    attributesData[j].setHeaderId(0);
                                    attributesData[j].setItemId(0);
                                    attributesData[j].setName("Please Select One");
                                } else {
                                    attributesData[j].setId(itemAttributeData.get(i).details.get(j - 1).id);
                                    attributesData[j].setShopId(itemAttributeData.get(i).details.get(j - 1).shop_id);
                                    attributesData[j].setHeaderId(itemAttributeData.get(i).details.get(j - 1).header_id);
                                    attributesData[j].setItemId(itemAttributeData.get(i).details.get(j - 1).item_id);
                                    attributesData[j].setAdditionalPrice(itemAttributeData.get(i).details.get(j - 1).additional_price);

                                    if (itemAttributeData.get(i).details.get(j - 1).additional_price.equals("0")) {
                                        attributesData[j].setName(itemAttributeData.get(i).details.get(j - 1).name);
                                    } else {
                                        attributesData[j].setName(itemAttributeData.get(i).details.get(j - 1).name + "(" +
                                                itemAttributeData.get(i).details.get(j - 1).additional_price +
                                                GlobalData.itemData.currency_symbol
                                                + ")");
                                    }
                                }

                            }


                            SpinAdapter adapter = new SpinAdapter(this, R.layout.spinner_item, attributesData);

                            spin.setAdapter(adapter);
                            spin.setOnItemSelectedListener(new Spinner.OnItemSelectedListener() {
                                @Override
                                public void onItemSelected(Spinner spinner, View view, int position, long l) {

                                    if (position != 0) {
                                        AttributeRowData attribute;
                                        if (!(spinner.getSelectedItem() == null)) {
                                            attribute = (AttributeRowData) spinner.getSelectedItem();

                                            selectedAttributeIds += attribute.getId() + "#";
                                            selectedAttributeName += attribute.getName() + ",";

                                            if(Float.parseFloat(attribute.getAdditionalPrice()) != 0.0) {
                                                attributePriceOnly =  Float.parseFloat(attribute.getAdditionalPrice());
                                            }

                                            additionalPrice = calcuatedPrice + attributePriceOnly;

                                            txtPrice.setText(getString(R.string.price) +
                                                    additionalPrice + GlobalData.itemData.currency_symbol + "(" + GlobalData.itemData.currency_short_form + ")");

                                            Utils.psLog("attributePriceOnly >> " + attributePriceOnly);

                                            ObjectAnimator animY = ObjectAnimator.ofFloat(txtPrice, "translationX", -10f, 0f);
                                            animY.setDuration(4000);
                                            animY.setInterpolator(new BounceInterpolator());

                                            animY.start();

                                        }
                                    }

                                }


                            });

                            attributeContainer.addView(spin, params);

                        }
                    }


                }

            } else {
                attributeTitleLayout.setVisibility(View.GONE);
            }
        }

    }

    private void bindDescription() {
        txtDescription.setText(GlobalData.itemData.description);
    }

    private void bindFavourite(FloatingActionButton fab) {
        try {
            if (pref.getInt("_login_user_id", 0) != 0) {
                final String URL = Config.APP_API_URL + Config.GET_FAVOURITE + GlobalData.itemData.id;

                HashMap<String, String> params = new HashMap<>();
                params.put("appuser_id", String.valueOf(pref.getInt("_login_user_id", 0)));
                //params.put("shop_id", String.valueOf(pref.getInt("_id", 0)));
                params.put("shop_id", String.valueOf(getIntent().getIntExtra("selected_shop_id", 0)));
                getFavourite(URL, params, fab);
            }
        } catch (Exception e) {
            Utils.psErrorLogE("Error in Bind Favourite.", e);
        }
    }

    public void bindRate() {

        try {
            String itemRatingCount = GlobalData.itemData.rating_count;
            setRatingBar.setRating(Float.parseFloat(itemRatingCount));
            String tmpRatingCount = "Total Rating : " + itemRatingCount;
            ratingCount.setText(tmpRatingCount);
        }catch (Exception e){
            Utils.psErrorLogE("Error in bind Rating", e);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Bind Data Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Private Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    private void getFavourite(final String postURL, final HashMap<String, String> params, final FloatingActionButton fab) {
        JsonObjectRequest req = new JsonObjectRequest(postURL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String success_status = response.getString("status");
                            String data_status = response.getString("data");

                            Utils.psLog("fav URL " + postURL);
                            Utils.psLog("fav Params " + params);
                            Utils.psLog("fav response " + data_status);

                            if (success_status.equals(jsonStatusSuccessString)) {
                                if(data_status.equals("yes")) {
                                    isFavourite = true;
                                    fab.setImageResource(R.drawable.ic_favorite_white);

                                    Utils.psLog("It is fav item.");
                                }
                            }

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                VolleyLog.e("Error: ", error.getMessage());
            }
        });

        // add the request object to the queue to be executed
        req.setShouldCache(false);
        VolleySingleton.getInstance(this).addToRequestQueue(req);
    }



    private void requestData(String uri) {
        JsonObjectRequest request = new JsonObjectRequest(uri,

                new Response.Listener<JSONObject>() {

                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String status = response.getString("status");
                            if (status.equals(jsonStatusSuccessString)) {

                                Gson gson = new Gson();
                                Type listType = new TypeToken<PItemData>() {
                                }.getType();
                                GlobalData.itemData = gson.fromJson(response.getString("data"), listType);

                                if (GlobalData.itemData != null) {
                                    bindData();
                                }

                            } else {

                                Utils.psLog("Error in loading.");
                            }
                        } catch (JSONException e) {

                            e.printStackTrace();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                },

                new Response.ErrorListener() {

                    @Override
                    public void onErrorResponse(VolleyError ex) {
                        Log.d("Volley Error ", ex.getMessage());
                    }
                });


        request.setShouldCache(false);
        VolleySingleton.getInstance(this).addToRequestQueue(request);

    }

    private void openGallery() {
        bundle = new Bundle();
        bundle.putParcelable("images", GlobalData.itemData);
        bundle.putString("from", "item");

        intent = new Intent(getApplicationContext(), GalleryActivity.class);
        intent.putExtra("images_bundle", bundle);
        startActivity(intent);
        overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
    }



    private void doSubmit(String postURL, HashMap<String, String> params, final String fromWhere) {
        JsonObjectRequest req = new JsonObjectRequest(postURL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String status = response.getString("status");
                            if (status.equals(jsonStatusSuccessString)) {

                                if (fromWhere.equals("like")) {

                                    GlobalData.itemData.like_count = response.getString("data");
                                    txtLikeCount.setText(" " + GlobalData.itemData.like_count + " ");
                                }
                            } else {
                                showFailPopup();
                            }

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                VolleyLog.e("Error: ", error.getMessage());
            }
        });

        // add the request object to the queue to be executed
        req.setShouldCache(false);
        VolleySingleton.getInstance(this).addToRequestQueue(req);
    }

    private void showFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.like_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    private void showRatingFailPopup() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.rating_fail);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    private void showCartEmpty() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.cart_empty);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    private void ratingChanged(float rating) {

        final String URL = Config.APP_API_URL + Config.POST_ITEM_RATING + selectedItemId;

        HashMap<String, String> params = new HashMap<>();
        params.put("appuser_id", String.valueOf(pref.getInt("_login_user_id", 0)));
        params.put("rating", String.valueOf(rating));
        params.put("shop_id", String.valueOf(pref.getInt("_id", 0)));

        JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String success_status = response.getString("status");

                            if (success_status.equals(jsonStatusSuccessString)) {
                                setRatingBar.setRating(Float.parseFloat(response.getString("data")));
                                String tmpRatingCount = "Total Rating : " + response.getString("data");
                                ratingCount.setText(tmpRatingCount);
                            } else {
                                showRatingFailPopup();
                            }

                        } catch (JSONException e) {
                            e.printStackTrace();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
               Utils.psErrorLogE("Error in rating Change.", error);
            }
        });

        // add the request object to the queue to be executed
        req.setShouldCache(false);
        VolleySingleton.getInstance(this).addToRequestQueue(req);

    }

    private void showNeedLogin() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.login_required);
        builder.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                Intent intent = new Intent(getApplicationContext(), UserLoginActivity.class);
                startActivity(intent);
                Utils.psLog("OK clicked.");
            }
        });
        builder.show();
    }

    private void showRequiredQty() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.qty_required);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    private void showRequiredDifferentQty() {
        AlertDialog.Builder builder =
                new AlertDialog.Builder(this, R.style.AppCompatAlertDialogStyle);
        builder.setTitle(R.string.sorry_title);
        builder.setMessage(R.string.qty_different_required);
        builder.setPositiveButton(R.string.OK, null);
        builder.show();
    }

    // Method to share either text or URL.
    private void shareTextUrl() {
        Intent share = new Intent(android.content.Intent.ACTION_SEND);
        share.setType("text/plain");

        // Add data to the intent, the receiving app will decide
        // what to do with it.
        share.putExtra(Intent.EXTRA_SUBJECT, getResources().getString(R.string.app_name));

        share.putExtra(Intent.EXTRA_TEXT, "http://codecanyon.net/user/panacea-soft/portfolio");

        startActivity(Intent.createChooser(share, "Share link!"));
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Private Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------------------------------------------------
    //region // Public Functions
    //-------------------------------------------------------------------------------------------------------------------------------------
    public void doPhoneCall(View view) {
        Utils.psLog("Calling Phone : " + txtPhone.getText());

        try {
            Intent intent = new Intent(Intent.ACTION_CALL, Uri.parse("tel:" + txtPhone.getText()));
            startActivity(intent);
        }catch (SecurityException se){
            Utils.psErrorLog("Error in calling phone. ", se);
        }
    }

    public void doEmail(View view) {
        final Intent emailIntent = new Intent(android.content.Intent.ACTION_SEND);
        emailIntent.setType("plain/text");
        emailIntent.putExtra(android.content.Intent.EXTRA_EMAIL, new String[]{pref.getString("_email", "")});
        emailIntent.putExtra(android.content.Intent.EXTRA_SUBJECT, "Hello");
        startActivity(Intent.createChooser(emailIntent, "Send mail..."));
    }

    public void doInquiry(View view) {
        final Intent intent;
        intent = new Intent(this, InquiryActivity.class);
        startActivity(intent);
        overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
    }

    public void doAddToCart() {

        if(editTextQty.getText().toString().matches("") || Integer.parseInt(editTextQty.getText().toString())== 0) {

            showRequiredQty();

        } else {

            if (pref.getInt("_login_user_id", 0) != 0) {

                Utils.psLog("Calculated Price : " + calcuatedPrice);

                if(isBasketItem() > 0) {
                    //Already Inside Basket

                    BasketData basket = db.getBasketById(GlobalData.itemData.id);

                    if(basket.getQty() != Integer.parseInt(editTextQty.getText().toString())) {

                        db.updateBasketByIds(new BasketData(
                                GlobalData.itemData.id,
                                GlobalData.itemData.shop_id,
                                pref.getInt("_login_user_id", 0),
                                GlobalData.itemData.name,
                                GlobalData.itemData.description,
                                String.valueOf(calcuatedPrice + attributePriceOnly),
                                GlobalData.itemData.discount_percent,
                                Integer.parseInt(editTextQty.getText().toString()),
                                GlobalData.itemData.images.get(0).path,
                                GlobalData.itemData.currency_symbol,
                                GlobalData.itemData.currency_short_form,
                                Utils.removeLastChar(selectedAttributeName),
                                Utils.removeLastChar(selectedAttributeIds)
                        ), GlobalData.itemData.id, GlobalData.itemData.shop_id);
                        Utils.psLog("Update Basket");

                    } else {

                        showRequiredDifferentQty();

                    }



                } else {
                    //New Item Insert Into Basket
                    db.addBasket(new BasketData(
                            GlobalData.itemData.id,
                            GlobalData.itemData.shop_id,
                            pref.getInt("_login_user_id", 0),
                            GlobalData.itemData.name,
                            GlobalData.itemData.description,
                            String.valueOf(calcuatedPrice + attributePriceOnly),
                            GlobalData.itemData.discount_percent,
                            Integer.parseInt(editTextQty.getText().toString()),
                            GlobalData.itemData.images.get(0).path,
                            GlobalData.itemData.currency_symbol,
                            GlobalData.itemData.currency_short_form,
                            Utils.removeLastChar(selectedAttributeName),
                            Utils.removeLastChar(selectedAttributeIds)
                    ));
                    Utils.psLog("New Item Basket");
                }
                menuItem.setVisible(true);
                updateCartBadgeCount();

            } else {
                showNeedLogin();
            }

            // Reading All Basket Items
            List<BasketData> baskets = db.getAllBasketData();

            for (BasketData basketData : baskets) {

                Utils.psLog(" id : " + basketData.getId() + ", item_id : " + basketData.getItemId() +
                ", shop_id : " + basketData.getShopId() + " user_id : " + basketData.getUserId() +
                ", name : " + basketData.getName() + ", desc : " + basketData.getDesc() + ", price : " + basketData.getUnitPrice() +
                ", discount : " + basketData.getDiscountPercent() + ", qty : " + basketData.getQty() + ", image_path : " + basketData.getImagePath() +
                ", currency_symbol : " + basketData.getCurrencySymbol() + ", currency_short_form : " + basketData.getCurrencyShortForm() +
                ", attribute_name : " + basketData.getSelectedAttributeNames() + ", attribute_id : " + basketData.getSelectedAttributeIds());
            }

        }

   }

    public void updateCartBadgeCount() {

        if (pref.getInt("_login_user_id", 0) != 0) {
            basketCount = db.getBasketCountByShopId(GlobalData.itemData.shop_id);
            if (basketCount > 0) {
                menuItem.setIcon(Utils.buildCounterDrawable(basketCount, R.drawable.ic_shopping_cart_white, this));
            }
        }else {
            menuItem.setVisible(false);
        }
    }

    public void doReview(View view) {
        ArrayList<PReviewData> itemReviewData = GlobalData.itemData.reviews;

        if(itemReviewData != null) {
            if (itemReviewData.size() > 0) {
                Intent intent = new Intent(this, ReviewListActivity.class);
                intent.putExtra("selected_item_id", selectedItemId);
                intent.putExtra("selected_shop_id", selectedShopId);
                startActivityForResult(intent, 1);
                overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
            } else {
                if (pref.getInt("_login_user_id", 0) != 0) {
                    Intent intent = new Intent(this, ReviewEntry.class);
                    intent.putExtra("selected_item_id", selectedItemId);
                    intent.putExtra("selected_shop_id", selectedShopId);
                    startActivityForResult(intent, 1);
                    overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
                } else {
                    Intent intent = new Intent(this, UserLoginActivity.class);
                    startActivity(intent);
                    overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
                }
            }
        }
    }

    public void doFavourite(View view) {
        if (pref.getInt("_login_user_id", 0) != 0) {
            final String URL = Config.APP_API_URL + Config.POST_ITEM_FAVOURITE + GlobalData.itemData.id;
            HashMap<String, String> params = new HashMap<>();
            params.put("appuser_id", String.valueOf(pref.getInt("_login_user_id", 0)));
            params.put("shop_id", String.valueOf(getIntent().getIntExtra("selected_shop_id", 0)));
            params.put("platformName","android");
            doSubmit(URL, params, "favourite");
        } else {
            if (isFavourite) {
                isFavourite = false;
                fab.setImageResource(R.drawable.ic_favorite_border);
            } else {
                isFavourite = true;
                fab.setImageResource(R.drawable.ic_favorite_white);
            }
            showNeedLogin();
        }
    }

    public void doLike(View view) {
        if (pref.getInt("_login_user_id", 0) != 0) {
            final String URL = Config.APP_API_URL + Config.POST_ITEM_LIKE + GlobalData.itemData.id;
            Utils.psLog(URL);
            HashMap<String, String> params = new HashMap<>();
            params.put("appuser_id", String.valueOf(pref.getInt("_login_user_id", 0)));
            params.put("shop_id", String.valueOf(pref.getInt("_id", 0)));
            params.put("platformName", "android");


            doSubmit(URL, params, "like");
        } else {
            showNeedLogin();
        }
    }

    public void doShare(View view) {

        shareTextUrl();

    }

    public int isBasketItem() {
        if (pref.getInt("_login_user_id", 0) != 0) {
            return db.getBasketCountByItemId(GlobalData.itemData.id);
        }
        return 0;
    }

    public void doShopInfo(View view) {
        final Intent intent;
        intent = new Intent(this, ShopInfoActivity.class);
        intent.putExtra("selected_item_id", selectedItemId);
        intent.putExtra("selected_shop_id", selectedShopId);
        startActivity(intent);
        overridePendingTransition(R.anim.right_to_left, R.anim.blank_anim);
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //endregion Public Functions
    //-------------------------------------------------------------------------------------------------------------------------------------

}
