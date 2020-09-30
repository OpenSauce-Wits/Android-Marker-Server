package com.example.stringsandviewsapplication;


import android.content.res.Resources;
import android.view.View;

import androidx.test.ext.junit.runners.AndroidJUnit4;
import androidx.test.rule.ActivityTestRule;

import org.hamcrest.Matcher;
import org.junit.Rule;
import org.junit.Test;
import org.junit.runner.RunWith;

import static androidx.test.espresso.Espresso.onData;
import static androidx.test.espresso.Espresso.onView;
import static androidx.test.espresso.action.ViewActions.click;
import static androidx.test.espresso.action.ViewActions.typeText;
import static androidx.test.espresso.assertion.ViewAssertions.matches;
import static androidx.test.espresso.matcher.ViewMatchers.withId;
import static androidx.test.espresso.matcher.ViewMatchers.withResourceName;
import static androidx.test.espresso.matcher.ViewMatchers.withText;
import static org.junit.Assert.*;


@RunWith(AndroidJUnit4.class)
public class ExampleInstrumentedTest {
    @Rule
    public ActivityTestRule<MainActivity> mainActivityActivityTestRule = new ActivityTestRule<>(MainActivity.class);

    @Test
    public void DefaultTextTest(){
        onView(withId(R.id.StringView)).check(matches(withText(R.string.def_string)));
        //testing if string actually references string attribute
//        onView(withId(R.string.def_string)).perform(typeText("Changed Default Text"));
//        onView(withId(R.id.StringView)).check(matches(withText(R.string.def_string)));


    }

    @Test
    public void ChangedTextTest(){
        onView(withId(R.id.StringView)).check(matches(withText(R.string.def_string)));
        onView(withId(R.id.StringView)).perform(click());
        onView(withId(R.id.StringView)).check(matches(withText(R.string.chng_string)));

        //testing if string actually references string attribute
//        onView(withId(R.string.def_string)).perform(typeText("Further Changed Text"));
//        onView(withId(R.id.StringView)).perform(click());
//        onView(withId(R.id.StringView)).check(matches(withText(R.string.chng_string)));
    }
}