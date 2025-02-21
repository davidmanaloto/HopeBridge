package com.example.hopebridge

import android.content.Intent
import android.graphics.drawable.Drawable
import android.os.Bundle
import android.text.InputType
import android.view.MotionEvent
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import okhttp3.ResponseBody
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback

interface SignupService {
    @FormUrlEncoded
    @POST("hopebridge/signup.php")
    fun signup(
        @Field("email") email: String,
        @Field("username") username: String,
        @Field("password") password: String
    ): Call<ResponseBody>
}

class Signup : AppCompatActivity() {
    private lateinit var passwordEditText: EditText
    private var isPasswordVisible = false
    private lateinit var emailEditText: EditText
    private lateinit var usernameEditText: EditText


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_signup)
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        emailEditText = findViewById(R.id.email)
        usernameEditText = findViewById(R.id.username)

        val signin = findViewById<Button>(R.id.signin)
        val signupButton: Button = findViewById(R.id.loginButton)
        passwordEditText = findViewById(R.id.password)

        signupButton.setOnClickListener {
            val password = passwordEditText.text.toString()
            val email = emailEditText.text.toString()
            val username = usernameEditText.text.toString()

            // Check if any fields are empty
            if (email.isEmpty() || username.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "Please fill all fields", Toast.LENGTH_SHORT).show()
                return@setOnClickListener // Early return if validation fails
            }

            // Check for whitespace only in username
            if (username.trim().isEmpty()) {
                Toast.makeText(this, "Please enter a valid username", Toast.LENGTH_SHORT).show()
                return@setOnClickListener // Early return if validation fails
            }

            // Check for whitespace and punctuation in username
            if (!isValidUsername(username)) {
                Toast.makeText(this, "Username cannot contain spaces or punctuation", Toast.LENGTH_SHORT).show()
                return@setOnClickListener // Early return if validation fails
            }

            // Check for whitespace and punctuation in password
            if (!isValidPassword(password)) {
                Toast.makeText(this, "Password cannot contain spaces or punctuation", Toast.LENGTH_SHORT).show()
                return@setOnClickListener // Early return if validation fails
            }

            // Password length validation
            if (password.length < 6) {
                Toast.makeText(this, "Password must be at least 6 characters", Toast.LENGTH_SHORT).show()
                return@setOnClickListener // Early return if validation fails
            }

            // All checks passed, send the data
            sendSignupData(email, username, password)
        }

        signin.setOnClickListener {
            val intent = Intent(this@Signup, Signin::class.java)
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
    private fun sendSignupData(email: String, username: String, password: String) {
        val apiService = ApiClient.getRetrofitInstance().create(SignupService::class.java)

        apiService.signup(email, username, password).enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: retrofit2.Response<ResponseBody>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@Signup, "Signup successful!", Toast.LENGTH_SHORT).show()
                    val intent = Intent(this@Signup, Signin::class.java)
                    startActivity(intent)
                } else {
                    // Capture error information and show in Toast
                    val errorBody = response.errorBody()
                    if (errorBody != null) {
                        try {
                            val errorBodyString = errorBody.string()

                            // Extract the error message from the JSON
                            val jsonObject = JSONObject(errorBodyString)
                            val errorMessage = jsonObject.getString("error") // Assuming your PHP returns "error" key

                            Toast.makeText(this@Signup, errorMessage, Toast.LENGTH_LONG).show()
                        } catch (e: Exception) {
                            Toast.makeText(this@Signup, "Error parsing response: ${e.message}", Toast.LENGTH_LONG).show()
                        }
                    } else {
                        Toast.makeText(this@Signup, "Response error: ${response.message()}", Toast.LENGTH_LONG).show()
                    }
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@Signup, "Request failed: ${t.message}", Toast.LENGTH_LONG).show()
            }
        })
    }

    private fun isValidUsername(username: String): Boolean {
        // Check if username contains spaces or punctuation
        return !username.contains(Regex("[\\s\\p{Punct}]"))
    }

    private fun isValidPassword(password: String): Boolean {
        // Check if password contains spaces or punctuation
        return !password.contains(Regex("[\\s\\p{Punct}]"))
    }
}

