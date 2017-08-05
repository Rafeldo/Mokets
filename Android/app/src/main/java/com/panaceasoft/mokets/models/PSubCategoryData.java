package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PSubCategoryData implements Parcelable {

    public int id;

    public int cat_id;

    public int shop_id;

    public String name;

    public int is_published;

    public int ordering;

    public String added;

    public String updated;

    public String cover_image_file;

    public int cover_image_width;

    public int cover_image_height;

    protected PSubCategoryData(Parcel in) {
        id = in.readInt();
        cat_id = in.readInt();
        shop_id = in.readInt();
        name = in.readString();
        is_published = in.readInt();
        ordering = in.readInt();
        added = in.readString();
        updated = in.readString();
        cover_image_file = in.readString();
        cover_image_width = in.readInt();
        cover_image_height = in.readInt();
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(cat_id);
        dest.writeInt(shop_id);
        dest.writeString(name);
        dest.writeInt(is_published);
        dest.writeInt(ordering);
        dest.writeString(added);
        dest.writeString(updated);
        dest.writeString(cover_image_file);
        dest.writeInt(cover_image_width);
        dest.writeInt(cover_image_height);
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PSubCategoryData> CREATOR = new Parcelable.Creator<PSubCategoryData>() {
        @Override
        public PSubCategoryData createFromParcel(Parcel in) {
            return new PSubCategoryData(in);
        }

        @Override
        public PSubCategoryData[] newArray(int size) {
            return new PSubCategoryData[size];
        }
    };
}
