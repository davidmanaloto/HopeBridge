package com.example.hopebridge

import android.content.Intent
import android.content.SharedPreferences
import android.content.res.Resources
import android.os.Bundle
import android.view.MotionEvent
import android.widget.EditText
import android.text.InputType
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import okhttp3.ResponseBody
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import android.util.Log
import android.view.View
import androidx.activity.enableEdgeToEdge
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

interface LoginApi {
    @FormUrlEncoded
    @POST("hopebridge/login.php") // Your actual PHP endpoint
    fun login(
        @Field("email") email: String,
        @Field("password") password: String
    ): Call<ResponseBody>
}

class Signin : AppCompatActivity() {
    private lateinit var emailEditText: EditText
    private lateinit var passwordEditText: EditText
    private lateinit var loginButton: Button
    private lateinit var signUpButton: Button
    private lateinit var forgotPassButton: Button
    private var isPasswordVisible = false

    private lateinit var sharedPreferences: SharedPreferences

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_signin)
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        sharedPreferences = getSharedPreferences("userPrefs", MODE_PRIVATE)

        // Initialize the views
        emailEditText = findViewById(R.id.username)
        passwordEditText = findViewById(R.id.password)
        loginButton = findViewById(R.id.loginButton)
        signUpButton = findViewById(R.id.signup)
        forgotPassButton = findViewById(R.id.forgetpass)


        // Handle login button click
        loginButton.setOnClickListener {
            val email = emailEditText.text.toString()
            val password = passwordEditText.text.toString()

            if (email.isNotEmpty() && password.isNotEmpty()) {
                sendLoginData(email, password)
            } else {
                Toast.makeText(this, "Please fill all fields", Toast.LENGTH_SHORT).show()
            }
        }

        // Navigate to signup activity
        signUpButton.setOnClickListener {
            val intent = Intent(this, Signup::class.java)
            startActivity(intent)
        }

        // Navigate to forgot password activity
        forgotPassButton.setOnClickListener {
            val intent = Intent(this, ForgotPassword::class.java)
            startActivity(intent)
        }


        passwordEditText.setOnTouchListener { v, event ->
            val DRAWABLE_RIGHT = 2

            if (event.action == MotionEvent.ACTION_UP) {
                val drawableRight = passwordEditText.compoundDrawables[DRAWABLE_RIGHT]

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

    override fun onStart() {
        super.onStart()

        // Check if user is logged in when the activity starts
        if (sharedPreferences.getBoolean("is_logged_in", false)) {
            navigateToHomepage()
        }
    }

    private fun navigateToHomepage() {
        val username = sharedPreferences.getString("username", "")
        val userId = sharedPreferences.getInt("user_id", -1)
        val email = sharedPreferences.getString("email", "")

        // Redirect to WelcomeActivity
        val intent = Intent(this, Homepage::class.java)
        intent.putExtra("username", username)
        intent.putExtra("user_id", userId)
        intent.putExtra("email", email)
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK) // Clear the back stack
        startActivity(intent)
        finish() // Close Login activity
    }

    override fun onBackPressed() {
        // Navigate back to StartPage
        val intent = Intent(this, MainActivity::class.java)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_NEW_TASK)
        startActivity(intent)
        finish() // Optional: Finish the current activity to remove it from the back stack
    }

    private fun togglePasswordVisibility() {
        if (isPasswordVisible) {
            // Hide password
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyecnot, 0)
        } else {
            // Show password
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyec, 0)
        }
        isPasswordVisible = !isPasswordVisible
        passwordEditText.setSelection(passwordEditText.text.length) // Keep cursor position
    }
    // Function to send login data to the server
    private fun sendLoginData(email: String, password: String) {
        val loginApi = ApiClient.getRetrofitInstance().create(LoginApi::class.java)
        loginApi.login(email, password).enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: retrofit2.Response<ResponseBody>) {
                if (response.isSuccessful) {
                    val jsonResponse = response.body()?.string() ?: return
                    Log.d("LoginResponse", "Response JSON: $jsonResponse")
                    val jsonObject = JSONObject(jsonResponse)
                    val status = jsonObject.getString("status")

                    if (status == "success") {
                        val username = jsonObject.getString("username")
                        val id = jsonObject.getInt("id") // Get id from PHP response

                        // Save login state in SharedPreferences
                        val editor = sharedPreferences.edit()
                        editor.putBoolean("is_logged_in", true)
                        editor.putString("username", username)
                        editor.putInt("user_id", id) // Save as user_id
                        editor.putString("email", email) // Save email for later use
                        editor.apply()

                        Toast.makeText(this@Signin, "Hello, $username!", Toast.LENGTH_SHORT).show()

                        // Start WelcomeActivity with user details
                        val intent = Intent(this@Signin, Homepage::class.java)
                        intent.putExtra("username", username)
                        intent.putExtra("user_id", id) // Pass user_id to WelcomeActivity
                        intent.putExtra("email", email) // Assuming email is needed in WelcomeActivity
                        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK)
                        startActivity(intent)
                        finish() // Close Login activity
                    } else {
                        Toast.makeText(this@Signin, "Login failed: ${jsonObject.getString("message")}", Toast.LENGTH_SHORT).show()
                    }
                } else {
                    Toast.makeText(this@Signin, "Error: ${response.message()}", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@Signin, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}