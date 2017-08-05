package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PReviewData implements Parcelable {

    public int id;

    public int item_id;

    public int appuser_id;

    public int shop_id;

    public String review;

    public int status;

    public String added;

    public String appuser_name;

    public String profile_photo;


    protected PReviewData(Parcel in) {
        id = in.readInt();
        item_id = in.readInt();
        appuser_id = in.readInt();
        shop_id = in.readInt();
        review = in.readString();
        status = in.readInt();
        added = in.readString();
        appuser_name = in.readString();
        profile_photo = in.readString();
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(item_id);
        dest.writeInt(appuser_id);
        dest.writeInt(shop_id);
        dest.writeString(review);
        dest.writeInt(status);
        dest.writeString(added);
        dest.writeString(appuser_name);
        dest.writeString(profile_photo);
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PReviewData> CREATOR = new Parcelable.Creator<PReviewData>() {
        @Override
        public PReviewData createFromParcel(Parcel in) {
            return new PReviewData(in);
        }

        @Override
        public PReviewData[] newArray(int size) {
            return new PReviewData[size];
        }
    };
}
