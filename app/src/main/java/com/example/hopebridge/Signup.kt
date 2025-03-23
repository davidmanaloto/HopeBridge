package com.example.hopebridge

import android.content.Intent
import android.os.Bundle
import android.text.InputType
import android.view.MotionEvent
import android.view.View
import android.view.animation.AnimationUtils
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import okhttp3.ResponseBody
import org.json.JSONObject
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import retrofit2.Call
import retrofit2.Callback


class Signup : AppCompatActivity() {
    private lateinit var passwordEditText: EditText
    private var isPasswordVisible = false
    private lateinit var emailEditText: EditText
    private lateinit var usernameEditText: EditText
    private lateinit var userBtn: Button
    private lateinit var orgBtn: Button
    private lateinit var userSignInLayout: LinearLayout
    private lateinit var orgSignInLayout: LinearLayout
    private lateinit var organizationEditText: EditText


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_signup)

        emailEditText = findViewById(R.id.email)
        usernameEditText = findViewById(R.id.username)
        passwordEditText = findViewById(R.id.password)
        val signupButton: Button = findViewById(R.id.btnSignUp)
        userBtn = findViewById(R.id.userbtn)
        orgBtn = findViewById(R.id.orgbtn)
        userSignInLayout = findViewById(R.id.userSignInLayout)
        orgSignInLayout = findViewById(R.id.orgSignInLayout)
        organizationEditText = findViewById(R.id.organizationName)


        val defaultDrawable = ContextCompat.getDrawable(this, R.drawable.squarecorner)
        val selectedDrawable = ContextCompat.getDrawable(this, R.drawable.colorsquare)

        val fadeIn = AnimationUtils.loadAnimation(this, R.anim.fade_in)
        val fadeOut = AnimationUtils.loadAnimation(this, R.anim.fade_out)

        userSignInLayout.visibility = View.VISIBLE
        orgSignInLayout.visibility = View.GONE
        userBtn.background = selectedDrawable

        orgBtn.setOnClickListener {
            userSignInLayout.animate()
                .alpha(0f)
                .setDuration(fadeOut.duration)
                .withEndAction {
                    userSignInLayout.visibility = View.GONE
                    orgSignInLayout.visibility = View.VISIBLE
                    orgSignInLayout.alpha = 0f
                    orgSignInLayout.animate().alpha(1f).setDuration(fadeIn.duration).start()
                }
                .start()
            orgBtn.background = selectedDrawable
            userBtn.background = defaultDrawable
        }

        userBtn.setOnClickListener {
            orgSignInLayout.animate()
                .alpha(0f)
                .setDuration(fadeOut.duration)
                .withEndAction {
                    orgSignInLayout.visibility = View.GONE
                    userSignInLayout.visibility = View.VISIBLE
                    userSignInLayout.alpha = 0f
                    userSignInLayout.animate().alpha(1f).setDuration(fadeIn.duration).start()
                }
                .start()
            userBtn.background = selectedDrawable
            orgBtn.background = defaultDrawable
        }



        passwordEditText.setOnTouchListener { v, event ->
            if (event.action == MotionEvent.ACTION_UP) {
                val drawableRight = passwordEditText.compoundDrawables[2]
                if (drawableRight != null) {
                    val boundsWidth = drawableRight.bounds.width()
                    val drawableAreaStart = passwordEditText.right - boundsWidth - passwordEditText.paddingRight
                    if (event.rawX >= drawableAreaStart) {
                        togglePasswordVisibility()
                        return@setOnTouchListener true
                    }
                }
            }
            false
        }
    }

    private fun togglePasswordVisibility() {
        if (isPasswordVisible) {
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyecnot, 0)
        } else {
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyec, 0)
        }
        isPasswordVisible = !isPasswordVisible
        passwordEditText.setSelection(passwordEditText.text.length)
    }



}