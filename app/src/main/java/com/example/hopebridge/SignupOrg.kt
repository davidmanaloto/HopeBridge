package com.example.hopebridge

import android.content.Intent
import android.os.Bundle
import android.text.InputType
import android.view.MotionEvent
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import okhttp3.ResponseBody
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST

interface OrgSignupService {
    @FormUrlEncoded
    @POST("HopeBridge_Web/php/org_signup.php")
    fun signup(
        @Field("email") email: String,
        @Field("organization_name") organizationName: String,
        @Field("password") password: String
    ): Call<ResponseBody>
}

class SignupOrg : AppCompatActivity() {
    private lateinit var emailEditText: EditText
    private lateinit var organizationEditText: EditText
    private lateinit var passwordEditText: EditText
    private var isPasswordVisible = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_signup_org)

        emailEditText = findViewById(R.id.email)
        organizationEditText = findViewById(R.id.orga)
        passwordEditText = findViewById(R.id.password)
        val signupButton: Button = findViewById(R.id.signupbutton)
        val signinButton: Button = findViewById(R.id.signin)

        signupButton.setOnClickListener {
            val email = emailEditText.text.toString()
            val organizationName = organizationEditText.text.toString()
            val password = passwordEditText.text.toString()

            if (email.isEmpty() || organizationName.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "Please fill all fields", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (organizationName.trim().isEmpty()) {
                Toast.makeText(this, "Please enter a valid organization name", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (!isValidUsername(organizationName)) {
                Toast.makeText(this, "Organization name cannot contain spaces or punctuation", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (!isValidPassword(password)) {
                Toast.makeText(this, "Password cannot contain spaces or punctuation", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (password.length < 6) {
                Toast.makeText(this, "Password must be at least 6 characters", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            sendSignupData(email, organizationName, password)
        }

        signinButton.setOnClickListener {
            val intent = Intent(this@SignupOrg, Signin::class.java)
            startActivity(intent)
        }

        passwordEditText.setOnTouchListener { _, event ->
            if (event.action == MotionEvent.ACTION_UP) {
                val drawableRight = passwordEditText.compoundDrawablesRelative[2]
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

    private fun sendSignupData(email: String, organizationName: String, password: String) {
        val apiService = ApiClient.getRetrofitInstance().create(OrgSignupService::class.java)

        apiService.signup(email, organizationName, password).enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: retrofit2.Response<ResponseBody>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@SignupOrg, "Signup successful!", Toast.LENGTH_SHORT).show()
                    val intent = Intent(this@SignupOrg, Signin::class.java)
                    startActivity(intent)
                } else {
                    val errorBody = response.errorBody()
                    if (errorBody != null) {
                        try {
                            val errorBodyString = errorBody.string()
                            val jsonObject = JSONObject(errorBodyString)
                            val errorMessage = jsonObject.getString("error")
                            Toast.makeText(this@SignupOrg, errorMessage, Toast.LENGTH_LONG).show()
                        } catch (e: Exception) {
                            Toast.makeText(this@SignupOrg, "Error parsing response: ${e.message}", Toast.LENGTH_LONG).show()
                        }
                    } else {
                        Toast.makeText(this@SignupOrg, "Response error: ${response.message()}", Toast.LENGTH_LONG).show()
                    }
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@SignupOrg, "Request failed: ${t.message}", Toast.LENGTH_LONG).show()
            }
        })
    }

    private fun isValidUsername(username: String): Boolean {
        return !username.contains(Regex("[\\s\\p{Punct}]"))
    }

    private fun isValidPassword(password: String): Boolean {
        return !password.contains(Regex("[\\s\\p{Punct}]"))
    }
}