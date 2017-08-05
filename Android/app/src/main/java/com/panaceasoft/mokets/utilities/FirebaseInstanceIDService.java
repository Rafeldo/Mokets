package com.panaceasoft.mokets.utilities;

import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.FirebaseInstanceIdService;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class FirebaseInstanceIDService extends FirebaseInstanceIdService {

    @Override
    public void onTokenRefresh() {

        String token = FirebaseInstanceId.getInstance().getToken();
        Utils.psLog("token : " + token);

    }

}
