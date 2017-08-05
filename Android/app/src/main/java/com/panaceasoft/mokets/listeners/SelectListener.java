package com.panaceasoft.mokets.listeners;

import android.view.View;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */


public interface SelectListener {
    public void Select(View view, int position, CharSequence text);
    public void Select(View view, int position, CharSequence text, int id);
    public void Select(View view, int position, CharSequence text, int id, float additionalPrice);
}
