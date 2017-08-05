package com.panaceasoft.mokets.models;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class AttributeRowData {

    private int id;
    private int shopId;
    private int headerId;
    private int itemId;
    private String name;
    private String additionalPrice;

    public int getId() {
        return id;
    }

    public int getShopId() {
        return shopId;
    }

    public int getHeaderId() {
        return headerId;
    }

    public int getItemId() {
        return itemId;
    }

    public String getName() {
        return name;
    }

    public String getAdditionalPrice() {
        return additionalPrice;
    }

    public void setId(int id) {
        this.id = id;
    }

    public void setShopId(int shopId) {
        this.shopId = shopId;
    }

    public void setHeaderId(int headerId) {
        this.headerId = headerId;
    }

    public void setItemId(int itemId) {
        this.itemId = itemId;
    }

    public void setName(String name) {
        this.name = name;
    }

    public void setAdditionalPrice(String additionalPrice) {
        this.additionalPrice = additionalPrice;
    }
}
