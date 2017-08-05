package com.panaceasoft.mokets.utilities;

import android.content.Context;
import android.support.design.widget.AppBarLayout;
import android.support.design.widget.CoordinatorLayout;
import android.support.design.widget.FloatingActionButton;
import android.util.AttributeSet;
import android.util.Log;
import android.view.View;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */

public class ScrollingFABBehavior extends CoordinatorLayout.Behavior<FloatingActionButton> {
    private int toolbarHeight;
    private int showHeight;
    private int hideHeight;

    public ScrollingFABBehavior(Context context, AttributeSet attrs) {
        super(context, attrs);
        this.toolbarHeight = Utils.getToolbarHeight(context);
        //showHeight = -((toolbarHeightDP - (toolbarHeightDP/2)) + Utils.dpToPx(256));
        showHeight = -256 + (toolbarHeight + (toolbarHeight/2));
        Log.d("TEAMPS", "F : " + 256 + " T : "+ toolbarHeight);
        hideHeight = -(toolbarHeight * 2);
    }

    @Override
    public boolean layoutDependsOn(CoordinatorLayout parent, FloatingActionButton fab, View dependency) {
        return dependency instanceof AppBarLayout;
    }


    @Override
    public boolean onDependentViewChanged(CoordinatorLayout parent, FloatingActionButton fab, View dependency) {
        if (dependency instanceof AppBarLayout) {

            int currentapiVersion = android.os.Build.VERSION.SDK_INT;
            if (currentapiVersion >= android.os.Build.VERSION_CODES.HONEYCOMB) {

                CoordinatorLayout.LayoutParams lp = (CoordinatorLayout.LayoutParams) fab.getLayoutParams();
                int fabBottomMargin = lp.bottomMargin;
                int distanceToScroll = fab.getHeight() + fabBottomMargin;

                Log.d("TEAMPS", dependency.getY() + " - " + showHeight + " - toolbar : " + toolbarHeight);

                if (dependency.getY() < showHeight) {
                    fab.setVisibility(View.VISIBLE);
                } else if (dependency.getY() > hideHeight) {
                    if (fab.getVisibility() == View.VISIBLE) {
                        fab.setVisibility(View.GONE);
                    }
                }
                // float ratio = (float)dependency.getY()/(float)toolbarHeight;
                // fab.setTranslationY(distanceToScroll * ratio);
            }else{
                fab.setVisibility(View.VISIBLE);
            }
        }
        return true;
    }
}
