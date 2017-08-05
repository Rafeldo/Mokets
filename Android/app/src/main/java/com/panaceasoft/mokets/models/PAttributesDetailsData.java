package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PAttributesDetailsData implements Parcelable {
    public int id;
    public int shop_id;
    public int header_id;
    public int item_id;
    public String name;
    public String added;
    public String detailString;
    public String additional_price;

    protected PAttributesDetailsData(Parcel in) {
        id = in.readInt();
        shop_id = in.readInt();
        header_id = in.readInt();
        item_id = in.readInt();
        name = in.readString();
        added = in.readString();
        detailString = in.readString();
        additional_price = in.readString();
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(shop_id);
        dest.writeInt(header_id);
        dest.writeInt(item_id);
        dest.writeString(name);
        dest.writeString(added);
        dest.writeString(detailString);
        dest.writeString(additional_price);
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PAttributesDetailsData> CREATOR = new Parcelable.Creator<PAttributesDetailsData>() {
        @Override
        public PAttributesDetailsData createFromParcel(Parcel in) {
            return new PAttributesDetailsData(in);
        }

        @Override
        public PAttributesDetailsData[] newArray(int size) {
            return new PAttributesDetailsData[size];
        }
    };
}
