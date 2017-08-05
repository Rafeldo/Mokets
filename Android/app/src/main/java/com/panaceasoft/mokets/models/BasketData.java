package com.panaceasoft.mokets.models;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class BasketData {

    public int id;
    public int item_id;
    public int shop_id;
    public int user_id;
    public String name;
    public String desc;
    public String unit_price;
    public String discount_percent;
    public int qty;
    public String image_path;
    public String currency_symbol;
    public String currency_short_form;
    public String selected_attribute_names;
    public String selected_attribute_ids;

    public BasketData(int id, int item_id, int shop_id, int user_id, String name, String desc, String unit_price,
                      String discount_percent, int qty, String image_path, String currency_symbol, String currency_short_form,
                      String selected_attribute_names, String selected_attribute_ids) {
        this.id = id;
        this.item_id = item_id;
        this.shop_id = shop_id;
        this.user_id = user_id;
        this.name = name;
        this.desc = desc;
        this.unit_price = unit_price;
        this.discount_percent = discount_percent;
        this.qty = qty;
        this.image_path = image_path;
        this.currency_symbol = currency_symbol;
        this.currency_short_form = currency_short_form;
        this.selected_attribute_names = selected_attribute_names;
        this.selected_attribute_ids = selected_attribute_ids;
    }

    public BasketData(int item_id, int shop_id, int user_id, String name, String desc, String unit_price,
                      String discount_percent, int qty, String image_path, String currency_symbol, String currency_short_form,
                      String selected_attribute_names, String selected_attribute_ids) {

        this.item_id = item_id;
        this.shop_id = shop_id;
        this.user_id = user_id;
        this.name = name;
        this.desc = desc;
        this.unit_price = unit_price;
        this.discount_percent = discount_percent;
        this.qty = qty;
        this.image_path = image_path;
        this.currency_symbol = currency_symbol;
        this.currency_short_form = currency_short_form;
        this.selected_attribute_names = selected_attribute_names;
        this.selected_attribute_ids = selected_attribute_ids;
    }

    public BasketData() {

    }

    public int getId() {
        return id;
    }

    public int getItemId() {
        return item_id;
    }

    public int getShopId() {
        return shop_id;
    }

    public int getUserId() {
        return user_id;
    }

    public String getName() {
        return name;
    }

    public String getDesc() {
        return desc;
    }

    public String getUnitPrice() {
        return unit_price;
    }

    public String getDiscountPercent() {
        return discount_percent;
    }

    public int getQty() {
        return qty;
    }

    public String getImagePath() {
        return image_path;
    }

    public String getCurrencySymbol() {
        return currency_symbol;
    }

    public String getCurrencyShortForm() {
        return currency_short_form;
    }

    public String getSelectedAttributeNames() {
        return selected_attribute_names;
    }

    public String getSelectedAttributeIds() {
        return selected_attribute_ids;
    }

    public void setId(int id) {
        this.id = id;
    }

    public void setItemId(int item_id) {
        this.item_id = item_id;
    }

    public void setShopId(int shop_id) {
        this.shop_id = shop_id;
    }

    public void setUserId(int user_id) {
        this.user_id = user_id;
    }

    public void setName(String name) {
        this.name = name;
    }

    public void setDesc(String desc) {
        this.desc = desc;
    }

    public void setUnitPrice(String unit_price) {
        this.unit_price = unit_price;
    }

    public void setDiscountPercent(String discount_percent) {
        this.discount_percent = discount_percent;
    }

    public void setQty(int qty) {
        this.qty = qty;
    }

    public void setImagePath(String image_path) {
        this.image_path = image_path;
    }

    public void setCurrencySymbol(String currency_symbol) {
        this.currency_symbol = currency_symbol;
    }

    public void setCurrencyShortForm(String currency_short_form) {
        this.currency_short_form = currency_short_form;
    }

    public void setSelectedAttributeNames(String selected_attribute_names) {
        this.selected_attribute_names = selected_attribute_names;
    }

    public void setSelectedAttributeIds(String selected_attribute_ids) {
        this.selected_attribute_ids = selected_attribute_ids;
    }
}
