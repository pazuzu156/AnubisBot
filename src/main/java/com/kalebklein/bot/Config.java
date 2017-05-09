package com.kalebklein.bot;

import org.apache.commons.io.IOUtils;
import org.json.JSONObject;

import java.io.*;

/**
 * Created by kaleb on 5/9/17.
 */
public class Config
{
    private JSONObject _config;

    public Config()
    {
        String jsonContent = "";
        try (FileInputStream is = new FileInputStream("config.json")) {
            jsonContent = IOUtils.toString(is, "UTF-8");
        } catch (IOException ex) {

        }
        this._config = new JSONObject(jsonContent);
    }
    public String get(String key)
    {
        return this._config.getString(key);
    }
}
