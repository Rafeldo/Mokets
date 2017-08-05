package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PCategoryData implements Parcelable {

    public int id;

    public int shop_id;

    public String name;

    public int is_published;

    public int ordering;

    public String added;

    public String updated;

    public String cover_image_file;

    public int cover_image_width;

    public int cover_image_height;

    public ArrayList<PSubCategoryData> sub_categories;

    protected PCategoryData(Parcel in) {
        id = in.readInt();
        shop_id = in.readInt();
        name = in.readString();
        is_published = in.readInt();
        ordering = in.readInt();
        added = in.readString();
        updated = in.readString();
        cover_image_file = in.readString();
        cover_image_width = in.readInt();
        cover_image_height = in.readInt();
        if (in.readByte() == 0x01) {
            sub_categories = new ArrayList<PSubCategoryData>();
            in.readList(sub_categories, PSubCategoryData.class.getClassLoader());
        } else {
            sub_categories = null;
        }
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(shop_id);
        dest.writeString(name);
        dest.writeInt(is_published);
        dest.writeInt(ordering);
        dest.writeString(added);
        dest.writeString(updated);
        dest.writeString(cover_image_file);
        dest.writeInt(cover_image_width);
        dest.writeInt(cover_image_height);
        if (sub_categories == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(sub_categories);
        }
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PCategoryData> CREATOR = new Parcelable.Creator<PCategoryData>() {
        @Override
        public PCategoryData createFromParcel(Parcel in) {
            return new PCategoryData(in);
        }

        @Override
        public PCategoryData[] newArray(int size) {
            return new PCategoryData[size];
        }
    };
}
