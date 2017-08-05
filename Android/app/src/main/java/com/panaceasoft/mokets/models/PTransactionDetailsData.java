package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PTransactionDetailsData implements Parcelable {
    public int id;
    public int transaction_header_id;
    public int shop_id;
    public int item_id;
    public String item_name;
    public String item_attribute;
    public String unit_price;
    public String qty;
    public String discount_percent;
    public String added;

    protected PTransactionDetailsData(Parcel in) {
        id = in.readInt();
        transaction_header_id = in.readInt();
        shop_id = in.readInt();
        item_id = in.readInt();
        item_name = in.readString();
        item_attribute = in.readString();
        unit_price = in.readString();
        qty = in.readString();
        discount_percent = in.readString();
        added = in.readString();
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(shop_id);
        dest.writeInt(shop_id);
        dest.writeInt(item_id);
        dest.writeString(item_name);
        dest.writeString(item_attribute);
        dest.writeString(unit_price);
        dest.writeString(qty);
        dest.writeString(discount_percent);
        dest.writeString(added);
    }

    @SuppressWarnings("unused")
    public static final Creator<PTransactionDetailsData> CREATOR = new Creator<PTransactionDetailsData>() {
        @Override
        public PTransactionDetailsData createFromParcel(Parcel in) {
            return new PTransactionDetailsData(in);
        }

        @Override
        public PTransactionDetailsData[] newArray(int size) {
            return new PTransactionDetailsData[size];
        }
    };
}
