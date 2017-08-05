package com.panaceasoft.mokets.fragments;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.StaggeredGridLayoutManager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.panaceasoft.mokets.Config;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.activities.SubCategoryActivity;
import com.panaceasoft.mokets.adapters.ItemAdapter;
import com.panaceasoft.mokets.listeners.ClickListener;
import com.panaceasoft.mokets.listeners.RecyclerTouchListener;
import com.panaceasoft.mokets.models.PItemData;
import com.panaceasoft.mokets.models.PSubCategoryData;
import com.panaceasoft.mokets.utilities.Utils;
import com.pnikosis.materialishprogress.ProgressWheel;
import com.squareup.picasso.Picasso;
import com.panaceasoft.mokets.utilities.VolleySingleton;

import org.json.JSONException;
import org.json.JSONObject;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class TabFragment extends Fragment {

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Variables
     **------------------------------------------------------------------------------------------------*/
    public int selectedShopID;
    public int selectedSubCategoryID;
    private RecyclerView mRecyclerView;
    private StaggeredGridLayoutManager mLayoutManager;
    private ItemAdapter mAdapter;
    private ProgressWheel progressWheel;
    private PSubCategoryData subCategoryData;
    private List<PItemData> it;
    private List<PItemData> myDataset;
    private SwipeRefreshLayout swipeRefreshLayout;
    private String jsonStatusSuccess ;
    private Picasso p;
    private LinearLayout mainLayout;
    private TabFragment tabFragment = null;
    private int currentSize= 0;

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Variables
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - New Instance Function
     **------------------------------------------------------------------------------------------------*/
    public static TabFragment newInstance(PSubCategoryData subCategoryData, int CityID) {

        TabFragment tabFragment = new TabFragment();
        tabFragment.setData(subCategoryData, CityID);
        return tabFragment;
    }

    public TabFragment() {

    }

    public void setData(PSubCategoryData subCategoryData, int selectedCityID) {
        this.subCategoryData = subCategoryData;
        this.selectedShopID = selectedCityID;
        this.selectedSubCategoryID = subCategoryData.id;

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - New Instance Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Override Functions
     **------------------------------------------------------------------------------------------------*/
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_tab, container, false);

        initData();

        initUI(view);

        return view;
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
    }

    @Override
    public void onDestroy() {

        try {
            mRecyclerView = null;
            mLayoutManager = null;

            mAdapter = null;
            progressWheel = null;
            subCategoryData = null;
            it = null;

            myDataset = null;
            swipeRefreshLayout = null;
            jsonStatusSuccess = null;
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
     * Start Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    private void initData() {
        try {
            this.jsonStatusSuccess = getResources().getString(R.string.json_status_success);
            // Inflate the layout for this fragment
            Utils.psLog(Config.APP_API_URL + Config.ITEMS_BY_SUB_CATEGORY + selectedShopID + "/sub_cat_id/" + subCategoryData.id + "/item/all/count/" + Config.PAGINATION + "/form/0");
            requestData(Config.APP_API_URL + Config.ITEMS_BY_SUB_CATEGORY + selectedShopID + "/sub_cat_id/" + subCategoryData.id + "/item/all/count/" + Config.PAGINATION + "/form/0");

            p = new Picasso.Builder(getActivity()).build();

        }catch(Exception e){
            Utils.psErrorLogE("Error in initData.", e);
        }
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init Data Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    private void initUI(View view) {
        initProgressWheel(view);
        initRecyclerView(view);
        initSwipeRefreshLayout(view);
        initLoadMore(view);
    }

    private void initLoadMore(View view) {
        try {

            mainLayout = (LinearLayout) view.findViewById(R.id.tab_layout);
            mAdapter.setOnLoadMoreListener(new ItemAdapter.OnLoadMoreListener() {

                @Override
                public void onLoadMore() {
                    if(myDataset != null) {
                        //add progress item
                        int from = myDataset.size();

                        if (currentSize != from) {
                            currentSize = from;
                            myDataset.add(null);
                            mAdapter.notifyItemInserted(myDataset.size() - 1);
                            Log.d("API URL : ", Config.APP_API_URL + Config.ITEMS_BY_SUB_CATEGORY + 1 + "/sub_cat_id/" + subCategoryData.id + "/item/all/count/" + Config.PAGINATION + "/form/" + from);
                            requestData(Config.APP_API_URL + Config.ITEMS_BY_SUB_CATEGORY + 1 + "/sub_cat_id/" + subCategoryData.id + "/item/all/count/" + Config.PAGINATION + "/from/" + from);
                        }
                    }
                }

            });

            swipeRefreshLayout.post(new Runnable() {
                @Override
                public void run() {
                    swipeRefreshLayout.setRefreshing(true);
                }
            });

            startLoading();

        } catch (Exception e) {
            Utils.psErrorLogE("Error in initLoadMore.", e);
        }
    }

    private void initProgressWheel(View view) {
        progressWheel = (ProgressWheel) view.findViewById(R.id.progress_wheel);
    }

    private void initSwipeRefreshLayout(View view) {
        swipeRefreshLayout = (SwipeRefreshLayout) view.findViewById(R.id.swipe_refresh);
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                stopLoading();
            }
        });
    }

    private void initRecyclerView(View view) {
        mRecyclerView = (RecyclerView) view.findViewById(R.id.my_recycler_view);
        mRecyclerView.setHasFixedSize(true);

        mLayoutManager = new StaggeredGridLayoutManager(2, StaggeredGridLayoutManager.VERTICAL);
        mRecyclerView.setLayoutManager(mLayoutManager);
        myDataset = new ArrayList<>();

        mAdapter = new ItemAdapter(myDataset, mRecyclerView, p);
        mRecyclerView.setAdapter(mAdapter);

        mRecyclerView.addOnItemTouchListener(new RecyclerTouchListener(getActivity(), mRecyclerView, new ClickListener() {
            @Override
            public void onClick(View view, int position) {

                onItemClicked(position);
            }

            @Override
            public void onLongClick(View view, int position) {

            }
        }));
    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Init UI Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    private void requestData(String uri) {

        JsonObjectRequest request = new JsonObjectRequest(uri,

                new Response.Listener<JSONObject>() {

                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            String status = response.getString("status");
                            if (status.equals(jsonStatusSuccess)) {

                                if(myDataset != null) {


                                    Gson gson = new Gson();
                                    Type listType = new TypeToken<List<PItemData>>() {
                                    }.getType();
                                    it = gson.fromJson(response.getString("data"), listType);

                                    if(it != null) {
                                        if (myDataset.size() > 0) {
                                            myDataset.remove(myDataset.size() - 1);
                                            mAdapter.notifyItemRemoved(myDataset.size());
                                        }


                                        for (PItemData pItem : it) {

                                            myDataset.add(pItem);

                                        }

                                        mAdapter.notifyItemInserted(myDataset.size());
                                    }
                                    stopLoading();
                                    progressWheel.setVisibility(View.GONE);
                                    mAdapter.setLoaded();

                                }

                            } else {
                                if(myDataset != null) {
                                    if (myDataset.size() > 0) {
                                        myDataset.remove(myDataset.size() - 1);
                                        mAdapter.notifyItemRemoved(myDataset.size());
                                    }
                                }
                                stopLoading();
                                Utils.psLog("Error in loading Sub Categories.");
                            }
                        } catch (JSONException e) {
                            stopLoading();
                            e.printStackTrace();
                        } catch (Exception e){
                            Utils.psErrorLog("Error in loading.", e);
                        }
                    }
                },

                new Response.ErrorListener() {

                    @Override
                    public void onErrorResponse(VolleyError ex) {
                        //Log.d("Volley Error " , ex.getMessage());
                    }
                });

        request.setShouldCache(false);
        VolleySingleton.getInstance(getActivity()).addToRequestQueue(request);
    }

    private void startLoading() {
        try {
            swipeRefreshLayout.post(new Runnable() {
                @Override
                public void run() {
                    swipeRefreshLayout.setRefreshing(true);
                }
            });
        } catch (Exception e) {
        }
    }

    private void stopLoading() {
        try {
            if (swipeRefreshLayout.isRefreshing()) {
                swipeRefreshLayout.setRefreshing(false);
            }
        } catch (Exception e) {
        }

    }

    /**------------------------------------------------------------------------------------------------
     * End Block - Private Functions
     **------------------------------------------------------------------------------------------------*/

    /**------------------------------------------------------------------------------------------------
     * Start Block - Public Functions
     **------------------------------------------------------------------------------------------------*/

    public void onItemClicked(int position) {
        ((SubCategoryActivity) getActivity()).openActivity(myDataset.get(position).id, myDataset.get(position).shop_id);
    }

    public void refershLikeAndReview(int itemID, String likeCount, String reviewCount) {

        mAdapter.updateItemLikeAndReviewCount(itemID, likeCount, reviewCount);
        mAdapter.notifyDataSetChanged();
    }
    /**------------------------------------------------------------------------------------------------
     * End Block - Public Functions
     **------------------------------------------------------------------------------------------------*/




}
