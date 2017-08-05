package com.panaceasoft.mokets.utilities;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.panaceasoft.mokets.models.BasketData;


import java.util.ArrayList;
import java.util.List;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class DBHandler extends SQLiteOpenHelper {

    // Database Version
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "moketsdb";

    // Contacts table name
    private static final String TABLE_BASKET = "BasketData";

    // Basket Table Columns names
    private static final String KEY_ID            = "id";
    private static final String KEY_ITEM_ID       = "item_id";
    private static final String KEY_SHOP_ID       = "shop_id";
    private static final String KEY_USER_ID       = "user_id";
    private static final String KEY_NAME          = "name";
    private static final String KEY_DESC          = "desc";
    private static final String KEY_UNTI_PRICE    = "unit_price";
    private static final String KEY_DISCOUNT_PERCENT    = "discount_percent";
    private static final String KEY_QTY           = "qty";
    private static final String KEY_IMAGE_PATH    = "image_path";
    private static final String KEY_CURRENCY_SYMBOL    = "currency_symbol";
    private static final String KEY_CURRENCY_SHORT_FORM   = "currency_short_form";
    private static final String KEY_SELECTED_ATTRIBUTE_NAMES   = "selected_attribute_names";
    private static final String KEY_SELECTED_ATTRIBUTE_IDS   = "selected_attribute_ids";





    public DBHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {

        String CREATE_BASKET_TABLE = "CREATE TABLE " + TABLE_BASKET + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"
                + KEY_ITEM_ID + " INTEGER,"
                + KEY_SHOP_ID + " INTEGER,"
                + KEY_USER_ID + " INTEGER,"
                + KEY_NAME + " TEXT,"
                + KEY_DESC + " TEXT,"
                + KEY_UNTI_PRICE + " TEXT,"
                + KEY_DISCOUNT_PERCENT + " TEXT,"
                + KEY_QTY + " INTEGER,"
                + KEY_IMAGE_PATH + " TEXT,"
                + KEY_CURRENCY_SYMBOL + " TEXT,"
                + KEY_CURRENCY_SHORT_FORM + " TEXT,"
                + KEY_SELECTED_ATTRIBUTE_NAMES + " TEXT,"
                + KEY_SELECTED_ATTRIBUTE_IDS + " TEXT" + ")";

        db.execSQL(CREATE_BASKET_TABLE);


    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        // Drop older table if existed
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_BASKET);
        // Creating tables again
        onCreate(db);
    }

    // Adding new basket item
    public void addBasket(BasketData basketData) {
        SQLiteDatabase db = this.getWritableDatabase();

        ContentValues values = new ContentValues();
        values.put(KEY_ITEM_ID, basketData.getItemId());
        values.put(KEY_SHOP_ID, basketData.getShopId());
        values.put(KEY_USER_ID, basketData.getUserId());
        values.put(KEY_NAME, basketData.getName());
        values.put(KEY_DESC, basketData.getDesc());
        values.put(KEY_UNTI_PRICE, basketData.getUnitPrice());
        values.put(KEY_DISCOUNT_PERCENT, basketData.getDiscountPercent());
        values.put(KEY_QTY, basketData.getQty());
        values.put(KEY_IMAGE_PATH, basketData.getImagePath());
        values.put(KEY_CURRENCY_SYMBOL, basketData.getCurrencySymbol());
        values.put(KEY_CURRENCY_SHORT_FORM, basketData.getCurrencyShortForm());
        values.put(KEY_SELECTED_ATTRIBUTE_NAMES, basketData.getSelectedAttributeNames());
        values.put(KEY_SELECTED_ATTRIBUTE_IDS, basketData.getSelectedAttributeIds());


        // Inserting Row
        db.insert(TABLE_BASKET, null, values);
        db.close(); // Closing database connection

    }


    // Getting one basket
    public BasketData getBasketById(int itemId) {
        SQLiteDatabase db = this.getReadableDatabase();

        Cursor cursor = db.query(TABLE_BASKET, new String[]{KEY_ID,
                        KEY_ITEM_ID, KEY_SHOP_ID, KEY_USER_ID, KEY_NAME, KEY_DESC, KEY_UNTI_PRICE,
                        KEY_DISCOUNT_PERCENT, KEY_QTY, KEY_IMAGE_PATH, KEY_CURRENCY_SYMBOL, KEY_CURRENCY_SHORT_FORM,
                        KEY_SELECTED_ATTRIBUTE_NAMES, KEY_SELECTED_ATTRIBUTE_IDS}, KEY_ITEM_ID + "=?",
                new String[]{String.valueOf(itemId)}, null, null, null, null);

        if (cursor != null)
            cursor.moveToFirst();

        BasketData basket = new BasketData(
                Integer.parseInt(cursor.getString(0)),
                Integer.parseInt(cursor.getString(1)),
                Integer.parseInt(cursor.getString(2)),
                Integer.parseInt(cursor.getString(3)),
                cursor.getString(4),
                cursor.getString(5),
                cursor.getString(6),
                cursor.getString(7),
                Integer.parseInt(cursor.getString(8)),
                cursor.getString(9),
                cursor.getString(10),
                cursor.getString(11),
                cursor.getString(12),
                cursor.getString(13));
        return basket;
    }

    // Getting All Basket
    public List<BasketData> getAllBasketData() {
        List<BasketData> basketList = new ArrayList<BasketData>();
        // Select All Query
        String selectQuery = "SELECT * FROM " + TABLE_BASKET;

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                BasketData basketData = new BasketData();
                basketData.setId(Integer.parseInt(cursor.getString(0)));
                basketData.setItemId(Integer.parseInt(cursor.getString(1)));
                basketData.setShopId(Integer.parseInt(cursor.getString(2)));
                basketData.setUserId(Integer.parseInt(cursor.getString(3)));
                basketData.setName(cursor.getString(4));
                basketData.setDesc(cursor.getString(5));
                basketData.setUnitPrice(cursor.getString(6));
                basketData.setDiscountPercent(cursor.getString(7));
                basketData.setQty(Integer.parseInt(cursor.getString(8)));
                basketData.setImagePath(cursor.getString(9));
                basketData.setCurrencySymbol(cursor.getString(10));
                basketData.setCurrencyShortForm(cursor.getString(11));
                basketData.setSelectedAttributeNames(cursor.getString(12));
                basketData.setSelectedAttributeIds(cursor.getString(13));

                // Adding basket to list
                basketList.add(basketData);
            } while (cursor.moveToNext());
        }

        // return basket list
        return basketList;
    }

    // Getting All Basket
    public List<BasketData> getAllBasketDataByShopId(int shopId) {
        List<BasketData> basketList = new ArrayList<BasketData>();
        // Select All Query
        String selectQuery = "SELECT * FROM " + TABLE_BASKET + " Where " + KEY_SHOP_ID + " = " + shopId;

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                BasketData basketData = new BasketData();
                basketData.setId(Integer.parseInt(cursor.getString(0)));
                basketData.setItemId(Integer.parseInt(cursor.getString(1)));
                basketData.setShopId(Integer.parseInt(cursor.getString(2)));
                basketData.setUserId(Integer.parseInt(cursor.getString(3)));
                basketData.setName(cursor.getString(4));
                basketData.setDesc(cursor.getString(5));
                basketData.setUnitPrice(cursor.getString(6));
                basketData.setDiscountPercent(cursor.getString(7));
                basketData.setQty(Integer.parseInt(cursor.getString(8)));
                basketData.setImagePath(cursor.getString(9));
                basketData.setCurrencySymbol(cursor.getString(10));
                basketData.setCurrencyShortForm(cursor.getString(11));
                basketData.setSelectedAttributeNames(cursor.getString(12));
                basketData.setSelectedAttributeIds(cursor.getString(13));

                // Adding basket to list
                basketList.add(basketData);
            } while (cursor.moveToNext());
        }

        // return basket list
        return basketList;
    }


    //Getting QTY By Item ID and Shop ID
    public int getQTYByIds(int itemId, int shopId) {

        SQLiteDatabase db = this.getReadableDatabase();

        Cursor cursor = db.query(TABLE_BASKET, new String[]{KEY_ID,
                        KEY_ITEM_ID, KEY_SHOP_ID, KEY_USER_ID, KEY_NAME, KEY_DESC, KEY_UNTI_PRICE,
                        KEY_DISCOUNT_PERCENT, KEY_QTY, KEY_IMAGE_PATH, KEY_CURRENCY_SYMBOL, KEY_CURRENCY_SHORT_FORM,
                        KEY_SELECTED_ATTRIBUTE_NAMES, KEY_SELECTED_ATTRIBUTE_IDS}, KEY_ITEM_ID + "=? AND " + KEY_SHOP_ID + " = ?",
                new String[]{String.valueOf(itemId), String.valueOf(shopId)}, null, null, null, null);

        if (cursor != null)
            cursor.moveToFirst();


          return Integer.parseInt(cursor.getString(8));

    }

    // Getting basket Count
    public int getBasketCount() {
        String countQuery = "SELECT * FROM " + TABLE_BASKET;
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        cursor.close();

        // return count
        return cursor.getCount();
    }

    // Getting basket Count By Item ID
    public int getBasketCountByItemId(int itemId) {
        String countQuery = "SELECT * FROM " + TABLE_BASKET + " Where " + KEY_ITEM_ID + " = " + itemId;
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count = cursor.getCount();
        cursor.close();

        // return count
        return count;
    }

    // Getting basket Count By shop ID
    public int getBasketCountByShopId(int shopId) {
        String countQuery = "SELECT * FROM " + TABLE_BASKET + " Where " + KEY_SHOP_ID + " = " + shopId;
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count = cursor.getCount();
        cursor.close();

        // return count
        return count;
    }

    // Updating a basket
    public int updateBasketByIds(BasketData basketData, int itemId, int shopId) {
        SQLiteDatabase db = this.getWritableDatabase();

        ContentValues values = new ContentValues();
        values.put(KEY_ITEM_ID, basketData.getItemId());
        values.put(KEY_SHOP_ID, basketData.getShopId());
        values.put(KEY_USER_ID, basketData.getUserId());
        values.put(KEY_NAME, basketData.getName());
        values.put(KEY_DESC, basketData.getDesc());
        values.put(KEY_UNTI_PRICE, basketData.getUnitPrice());
        values.put(KEY_DISCOUNT_PERCENT, basketData.getDiscountPercent());
        values.put(KEY_QTY, basketData.getQty());
        values.put(KEY_IMAGE_PATH, basketData.getImagePath());
        values.put(KEY_CURRENCY_SYMBOL, basketData.getCurrencySymbol());
        values.put(KEY_CURRENCY_SHORT_FORM, basketData.getCurrencyShortForm());
        values.put(KEY_SELECTED_ATTRIBUTE_NAMES, basketData.getSelectedAttributeNames());
        values.put(KEY_SELECTED_ATTRIBUTE_IDS, basketData.getSelectedAttributeIds());

        // updating row
        return db.update(TABLE_BASKET, values, KEY_ITEM_ID + " = ? AND " + KEY_SHOP_ID + " = ?",
                new String[]{String.valueOf(itemId), String.valueOf(shopId)});
    }

    // Deleting a basket
    public void deleteBasket(BasketData basketData) {
        SQLiteDatabase db = this.getWritableDatabase();
        db.delete(TABLE_BASKET, KEY_ID + " = ?",
                new String[] { String.valueOf(basketData.getId()) });
        db.close();
    }

    // Deleting a basket by IDs
    public void deleteBasketByIds(int itemId, int shopId) {
        SQLiteDatabase db = this.getWritableDatabase();
        db.delete(TABLE_BASKET, KEY_ITEM_ID + " = ? AND " + KEY_SHOP_ID + " =? ",
                new String[] { String.valueOf(itemId), String.valueOf(shopId)  });
        db.close();
    }

    // Deleting a basket by Shop Id
    public void deleteBasketByShopId(int shopId) {
        SQLiteDatabase db = this.getWritableDatabase();
        db.delete(TABLE_BASKET, KEY_SHOP_ID + " =? ",
                new String[] {String.valueOf(shopId)});
        db.close();
    }

}
