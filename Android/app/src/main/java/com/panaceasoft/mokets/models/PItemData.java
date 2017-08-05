package com.panaceasoft.mokets.models;

import android.os.Parcel;
import android.os.Parcelable;

import java.util.ArrayList;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class PItemData implements Parcelable {

    public int id;

    public int cat_id;

    public int sub_cat_id;

    public int shop_id;

    public int discount_type_id;

    public String discount_percent;

    public String discount_name;

    public String name;

    public String description;

    public float unit_price;

    public String search_tag;

    public int is_published;

    public String added;

    public String updated;

    public String like_count;

    public String review_count;

    public String inquiries_count;

    public String rating_count;

    public String currency_symbol;

    public String currency_short_form;

    public ArrayList<PReviewData> reviews;

    public ArrayList<PImageData> images;

    public ArrayList<PAttributesData> attributes;


    protected PItemData(Parcel in) {
        id = in.readInt();
        cat_id = in.readInt();
        sub_cat_id = in.readInt();
        shop_id = in.readInt();
        discount_type_id = in.readInt();
        discount_name = in.readString();
        discount_percent = in.readString();
        name = in.readString();
        description = in.readString();
        unit_price = in.readFloat();
        search_tag = in.readString();
        is_published = in.readInt();
        added = in.readString();
        updated = in.readString();
        like_count = in.readString();
        review_count = in.readString();
        inquiries_count = in.readString();
        rating_count = in.readString();
        currency_symbol = in.readString();
        currency_short_form = in.readString();

        if (in.readByte() == 0x01) {
            reviews = new ArrayList<PReviewData>();
            in.readList(reviews, PReviewData.class.getClassLoader());
        } else {
            reviews = null;
        }

        if (in.readByte() == 0x01) {
            images = new ArrayList<PImageData>();
            in.readList(images, PImageData.class.getClassLoader());
        } else {
            images = null;
        }

        if (in.readByte() == 0x01) {
            attributes = new ArrayList<PAttributesData>();
            in.readList(attributes, PAttributesData.class.getClassLoader());
        } else {
            attributes = null;
        }

    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeInt(id);
        dest.writeInt(cat_id);
        dest.writeInt(sub_cat_id);
        dest.writeInt(shop_id);
        dest.writeInt(discount_type_id);
        dest.writeString(discount_name);
        dest.writeString(discount_percent);
        dest.writeString(name);
        dest.writeString(description);
        dest.writeFloat(unit_price);
        dest.writeString(search_tag);
        dest.writeInt(is_published);
        dest.writeString(added);
        dest.writeString(updated);
        dest.writeString(like_count);
        dest.writeString(review_count);
        dest.writeString(inquiries_count);
        dest.writeString(rating_count);
        dest.writeString(currency_symbol);
        dest.writeString(currency_short_form);


        if (reviews == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(reviews);
        }

        if (images == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(images);
        }

        if (attributes == null) {
            dest.writeByte((byte) (0x00));
        } else {
            dest.writeByte((byte) (0x01));
            dest.writeList(attributes);
        }

    }

    @SuppressWarnings("unused")
    public static final Parcelable.Creator<PItemData> CREATOR = new Parcelable.Creator<PItemData>() {
        @Override
        public PItemData createFromParcel(Parcel in) {
            return new PItemData(in);
        }

        @Override
        public PItemData[] newArray(int size) {
            return new PItemData[size];
        }
    };
}
