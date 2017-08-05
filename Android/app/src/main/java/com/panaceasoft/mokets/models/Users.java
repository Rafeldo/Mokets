package com.panaceasoft.mokets.models;

import java.sql.Blob;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class Users {

    int id;
    String user_name;
    String email;
    String about_me;
    int is_banded;
    String profile_photo;

    public Users() {
    }

    public Users(String user_name, String email, String about_me, int is_banded, String profile_photo) {
        this.user_name = user_name;
        this.email = email;
        this.about_me = about_me;
        this.is_banded = is_banded;
        this.profile_photo = profile_photo;
    }

    public Users(int id, String user_name, String email, String about_me, int is_banded, String profile_photo) {
        this.id = id;
        this.user_name = user_name;
        this.email = email;
        this.about_me = about_me;
        this.is_banded = is_banded;
        this.profile_photo = profile_photo;
    }

    public int getId() {
        return id;
    }

    public String getUser_name() {
        return user_name;
    }

    public String getEmail() {
        return email;
    }

    public String getAbout_me() {
        return about_me;
    }

    public int getIs_banded() {
        return is_banded;
    }

    public String getProfile_photo() {
        return profile_photo;
    }

    public void setId(int id) {
        this.id = id;
    }

    public void setUser_name(String user_name) {
        this.user_name = user_name;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public void setAbout_me(String about_me) {
        this.about_me = about_me;
    }

    public void setIs_banded(int is_banded) {
        this.is_banded = is_banded;
    }

    public void setProfile_photo(String profile_photo) {
        this.profile_photo = profile_photo;
    }

}
