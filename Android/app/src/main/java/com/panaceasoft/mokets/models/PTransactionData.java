package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PTransactionData implements Parcelable {
    public int id;
    public String shop_id;
    public String user_id;
    public String payment_trans_id;
    public String total_amount;
    public String delivery_address;
    public String billing_address;
    public String transaction_status;
    public String email;
    public String phone;
    public String payment_method;
    public String added;
    public String currency_symbol;
    public String currency_short_form;

    public ArrayList<PTransactionDetailsData> details;

    protected PTransactionData(Parcel in) {
        id = in.readInt();
        shop_id = in.readString();
        user_id = in.readString();
        payment_trans_id = in.readString();
        total_amount = in.readString();
        delivery_address = in.readString();
        billing_address = in.readString();
        transaction_status = in.readString();
        email = in.readString();
        phone = in.readString();
        payment_method = in.readString();
        added = in.readString();
        currency_symbol = in.readString();
        currency_short_form = in.readString();

        if (in.readByte() == 0x01) {
            details = new ArrayList();
            in.readList(details, PTransactionData.class.getClassLoader());
        } else {
            details = null;
        }
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeString(shop_id);
        dest.writeString(user_id);
        dest.writeString(payment_trans_id);
        dest.writeString(total_amount);
        dest.writeString(delivery_address);
        dest.writeString(billing_address);
        dest.writeString(transaction_status);
        dest.writeString(email);
        dest.writeString(phone);
        dest.writeString(payment_method);
        dest.writeString(added);
        dest.writeString(currency_symbol);
        dest.writeString(currency_short_form);

        if (details == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(details);
        }
    }

    @SuppressWarnings("unused")
    public static final Creator<PTransactionData> CREATOR = new Creator<PTransactionData>() {
        @Override
        public PTransactionData createFromParcel(Parcel in) {
            return new PTransactionData(in);
        }

        @Override
        public PTransactionData[] newArray(int size) {
            return new PTransactionData[size];
        }
    };
}
