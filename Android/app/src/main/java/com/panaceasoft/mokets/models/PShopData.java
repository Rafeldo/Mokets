package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PShopData implements Parcelable {

    public int id;

    public String name;

    public String description;

    public String phone;

    public String email;

    public String address;

    public String coordinate;

    public String lat;

    public String lng;

    public String paypal_email;

    public String paypal_environment;

    public String paypal_appid_live;

    public String paypal_merchantname;

    public String paypal_customerid;

    public String paypal_ipnurl;

    public String paypal_memo;


    public String bank_account;

    public String bank_name;

    public String bank_code;

    public String branch_code;

    public String swift_code;


    public String cod_email;

    public String currency_symbol;

    public String currency_short_form;



    public String added;

    public int status;

    public int item_count;

    public int category_count;

    public int sub_category_count;

    public int follow_count;



    public String cover_image_file;

    public int cover_image_width;

    public int cover_image_height;

    public String cover_image_description;

    public int cod_enabled;

    public int banktransfer_enabled;

    public int stripe_enabled;

    public String stripe_publishable_key;

    public String stripe_secret_key;

    public ArrayList<PCategoryData> categories;

    protected PShopData(Parcel in) {
        id = in.readInt();
        name = in.readString();
        description = in.readString();
        phone = in.readString();
        email = in.readString();
        address = in.readString();
        coordinate = in.readString();
        lat = in.readString();
        lng = in.readString();

        paypal_email = in.readString();
        paypal_environment = in.readString();
        paypal_appid_live = in.readString();
        paypal_merchantname = in.readString();
        paypal_customerid = in.readString();
        paypal_ipnurl = in.readString();
        paypal_memo = in.readString();

        bank_account = in.readString();
        bank_name = in.readString();
        bank_code = in.readString();
        branch_code = in.readString();
        swift_code = in.readString();

        cod_email = in.readString();
        currency_symbol = in.readString();
        currency_short_form = in.readString();


        added = in.readString();
        status = in.readInt();
        item_count = in.readInt();
        category_count = in.readInt();
        sub_category_count = in.readInt();
        follow_count = in.readInt();

        cover_image_file = in.readString();
        cover_image_width = in.readInt();
        cover_image_height = in.readInt();
        cover_image_description = in.readString();

        cod_enabled = in.readInt();
        banktransfer_enabled = in.readInt();
        stripe_enabled = in.readInt();

        stripe_publishable_key = in.readString();
        stripe_secret_key = in.readString();

        if (in.readByte() == 0x01) {
            categories = new ArrayList<PCategoryData>();
            in.readList(categories, PCategoryData.class.getClassLoader());
        } else {
            categories = null;
        }
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeString(name);
        dest.writeString(description);
        dest.writeString(phone);
        dest.writeString(email);
        dest.writeString(address);
        dest.writeString(coordinate);
        dest.writeString(lat);
        dest.writeString(lng);

        dest.writeString(paypal_email);
        dest.writeString(paypal_environment);
        dest.writeString(paypal_appid_live);
        dest.writeString(paypal_merchantname);
        dest.writeString(paypal_customerid);
        dest.writeString(paypal_ipnurl);
        dest.writeString(paypal_memo);

        dest.writeString(added);
        dest.writeInt(status);
        dest.writeInt(item_count);
        dest.writeInt(category_count);
        dest.writeInt(sub_category_count);
        dest.writeInt(follow_count);
        dest.writeString(cover_image_file);
        dest.writeInt(cover_image_width);
        dest.writeInt(cover_image_height);
        dest.writeString(cover_image_description);

        dest.writeInt(cod_enabled);
        dest.writeInt(stripe_enabled);
        dest.writeInt(banktransfer_enabled);

        dest.writeString(stripe_publishable_key);
        dest.writeString(stripe_secret_key);

        if (categories == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(categories);
        }
    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PShopData> CREATOR = new Parcelable.Creator<PShopData>() {
        @Override
        public PShopData createFromParcel(Parcel in) {
            return new PShopData(in);
        }

        @Override
        public PShopData[] newArray(int size) {
            return new PShopData[size];
        }
    };
}
