package com.example.stringsandviewsapplication;

import androidx.appcompat.app.AppCompatActivity;

import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        final TextView StringView = (TextView) findViewById(R.id.StringView);
        StringView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                StringView.setText(R.string.chng_string);
            }
        });
    }
}