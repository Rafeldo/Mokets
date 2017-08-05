package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PNewsData implements Parcelable {
    public int id;
    public int city_id;
    public String title;
    public String description;
    public int is_published;
    public String added;
    public ArrayList<PImageData> images;

    protected PNewsData(Parcel in) {
        id = in.readInt();
        city_id = in.readInt();
        title = in.readString();
        description = in.readString();
        is_published = in.readInt();
        added = in.readString();
        if (in.readByte() == 0x01) {
            images = new ArrayList<PImageData>();
            in.readList(images, PImageData.class.getClassLoader());
        } else {
            images = null;
        }
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(city_id);
        dest.writeString(title);
        dest.writeString(description);
        dest.writeInt(is_published);
        dest.writeString(added);
        if (images == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(images);
        }
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PNewsData> CREATOR = new Parcelable.Creator<PNewsData>() {
        @Override
        public PNewsData createFromParcel(Parcel in) {
            return new PNewsData(in);
        }

        @Override
        public PNewsData[] newArray(int size) {
            return new PNewsData[size];
        }
    };
}
