package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PImageData implements Parcelable {

    public int id;

    public int parent_id;

    public int shop_id;

    public String type;

    public String path;

    public float width;

    public float height;

    public String description;


    protected PImageData(Parcel in) {
        id = in.readInt();
        parent_id = in.readInt();
        shop_id = in.readInt();
        type = in.readString();
        path = in.readString();
        width = in.readFloat();
        height = in.readFloat();
        description = in.readString();
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(parent_id);
        dest.writeInt(shop_id);
        dest.writeString(type);
        dest.writeString(path);
        dest.writeFloat(width);
        dest.writeFloat(height);
        dest.writeString(description);
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PImageData> CREATOR = new Parcelable.Creator<PImageData>() {
        @Override
        public PImageData createFromParcel(Parcel in) {
            return new PImageData(in);
        }

        @Override
        public PImageData[] newArray(int size) {
            return new PImageData[size];
        }
    };
}
