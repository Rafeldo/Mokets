package com.panaceasoft.mokets.models;

import com.google.gson.annotations.SerializedName;

import java.io.Serializable;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class ReviewData implements Serializable {

    @SerializedName("id")
    private int id;

    @SerializedName("item_id")
    private int item_id;

    @SerializedName("appuser_id")
    private int appuser_id;

    @SerializedName("city_id")
    private int shop_id;

    @SerializedName("review")
    private String review;

    @SerializedName("status")
    private int status;

    @SerializedName("added")
    private String added;

    @SerializedName("appuser_name")
    private String appuser_name;

    @SerializedName("profile_photo")
    private String profile_photo;

    public int getId() {
        return id;
    }

    public int getItem_id() {
        return item_id;
    }

    public int getAppuser_id() {
        return appuser_id;
    }

    public int getShop_id() {
        return shop_id;
    }

    public String getReview() {
        return review;
    }

    public int getStatus() {
        return status;
    }

    public String getAdded() {
        return added;
    }

    public String getAppuser_name() {
        return appuser_name;
    }

    public String getProfile_photo() {
        return profile_photo;
    }

    public void setId(int id) {
        this.id = id;
    }

    public void setItem_id(int item_id) {
        this.item_id = item_id;
    }

    public void setAppuser_id(int appuser_id) {
        this.appuser_id = appuser_id;
    }

    public void setShop_id(int shop_id) {
        this.shop_id = shop_id;
    }

    public void setReview(String review) {
        this.review = review;
    }

    public void setStatus(int status) {
        this.status = status;
    }

    public void setAdded(String added) {
        this.added = added;
    }

    public void setAppuser_name(String appuser_name) {
        this.appuser_name = appuser_name;
    }

    public void setProfile_photo(String profile_photo) {
        this.profile_photo = profile_photo;
    }
}
