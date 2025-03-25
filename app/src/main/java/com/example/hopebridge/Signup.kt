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
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import okhttp3.ResponseBody
import org.json.JSONObject
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response


interface UserApiService {
    @FormUrlEncoded
    @POST("HopeBridge_Web/php/signup.php") // Make sure this matches the PHP file's endpoint
    fun signupUser(
        @Field("email") email: String,
        @Field("username") username: String,
        @Field("password") password: String
    ): Call<ResponseBody>
}


class Signup : AppCompatActivity() {
    private lateinit var passwordEditText: EditText
    private lateinit var emailEditText: EditText
    private lateinit var usernameEditText: EditText
    private lateinit var signupButton: Button
    private var isPasswordVisible = false
    private lateinit var orgPassEditText: EditText
    private var isOrgPasswordVisible = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_signup)

        val userBtn = findViewById<Button>(R.id.userbtn)
        val orgBtn = findViewById<Button>(R.id.orgbtn)
        val userSignInLayout = findViewById<LinearLayout>(R.id.userSignInLayout)
        val orgSignInLayout = findViewById<LinearLayout>(R.id.orgSignInLayout)
        val signinButton: Button = findViewById(R.id.signin)
        val orgsigninButton: Button = findViewById(R.id.orgsignin)
        val nextButton: Button = findViewById(R.id.orgNextPage)
        emailEditText = findViewById(R.id.email)
        usernameEditText = findViewById(R.id.username)
        passwordEditText = findViewById(R.id.password)
        signupButton = findViewById(R.id.btnSignUp)
        orgPassEditText = findViewById(R.id.orgPass)


        val defaultDrawable = ContextCompat.getDrawable(this, R.drawable.squarecorner)
        val selectedDrawable = ContextCompat.getDrawable(this, R.drawable.colorsquare)

        // Show user sign-in form by default
        userSignInLayout.visibility = View.VISIBLE
        orgSignInLayout.visibility = View.GONE
        userBtn.background = selectedDrawable

        // Load animations
        val fadeIn = AnimationUtils.loadAnimation(this, R.anim.fade_in)
        val fadeOut = AnimationUtils.loadAnimation(this, R.anim.fade_out)

        signupButton.setOnClickListener {
            val email = emailEditText.text.toString().trim()
            val username = usernameEditText.text.toString().trim()
            val password = passwordEditText.text.toString().trim()

            if (email.isEmpty() || username.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "All fields are required!", Toast.LENGTH_SHORT).show()
            } else {
                registerUser(email, username, password)
            }
        }

        signinButton.setOnClickListener {
            val intent = Intent(this@Signup, MainActivity::class.java)
            startActivity(intent)
        }

        orgsigninButton.setOnClickListener {
            val intent = Intent(this@Signup, MainActivity::class.java)
            startActivity(intent)
        }

        nextButton.setOnClickListener {
            val orgEmailField = findViewById<EditText>(R.id.orgEmail)
            val orgNameField = findViewById<EditText>(R.id.orgName)
            val orgPassField = findViewById<EditText>(R.id.orgPass)

            if (orgEmailField == null || orgNameField == null || orgPassField == null) {
                Toast.makeText(this, "Some fields are missing!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val orgEmail = orgEmailField.text.toString().trim()
            val orgName = orgNameField.text.toString().trim()
            val orgPass = orgPassField.text.toString().trim()

            if (orgEmail.isEmpty() || orgName.isEmpty() || orgPass.isEmpty()) {
                Toast.makeText(this, "Please fill in all fields", Toast.LENGTH_SHORT).show()
            } else {
                val intent = Intent(this@Signup, ReqOrg::class.java)
                intent.putExtra("orgEmail", orgEmail)
                intent.putExtra("orgName", orgName)
                intent.putExtra("orgPass", orgPass)
                startActivity(intent)
            }
        }

        orgPassEditText.setOnTouchListener { _, event ->
            if (event.action == MotionEvent.ACTION_UP) {
                val drawableRight = orgPassEditText.compoundDrawablesRelative[2]
                if (drawableRight != null) {
                    val boundsWidth = drawableRight.bounds.width()
                    val drawableAreaStart = orgPassEditText.right - boundsWidth - orgPassEditText.paddingRight
                    if (event.rawX >= drawableAreaStart) {
                        toggleOrgPasswordVisibility()
                        return@setOnTouchListener true
                    }
                }
            }
            false
        }

        orgBtn.setOnClickListener { _: View? ->
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

        userBtn.setOnClickListener { _: View? ->
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

        passwordEditText.setOnTouchListener { _, event ->
            if (event.action == MotionEvent.ACTION_UP) {
                val drawableRight = passwordEditText.compoundDrawables[2]
                if (drawableRight != null) {
                    val boundsWidth = drawableRight.bounds.width()
                    val drawableAreaStart =
                        passwordEditText.right - boundsWidth - passwordEditText.paddingRight
                    if (event.rawX >= drawableAreaStart) {
                        togglePasswordVisibility()
                        return@setOnTouchListener true
                    }
                }
            }
            false
        }
    }



    private fun registerUser(email: String, username: String, password: String) {
        val apiService = ApiClient.getRetrofitInstance().create(UserApiService::class.java)
        val call = apiService.signupUser(email, username, password)

        call.enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    try {
                        val responseBody = response.body()?.string()
                        val jsonResponse = JSONObject(responseBody ?: "")
                        if (jsonResponse.has("message")) {
                            Toast.makeText(
                                this@Signup,
                                jsonResponse.getString("message"),
                                Toast.LENGTH_SHORT
                            ).show()
                            startActivity(Intent(this@Signup, MainActivity::class.java))
                            finish()
                        } else {
                            Toast.makeText(this@Signup, "Unexpected response", Toast.LENGTH_SHORT)
                                .show()
                        }
                    } catch (e: Exception) {
                        e.printStackTrace()
                        Toast.makeText(this@Signup, "Error parsing response", Toast.LENGTH_SHORT)
                            .show()
                    }
                } else {
                    try {
                        val errorBody = response.errorBody()?.string()
                        val errorResponse = JSONObject(errorBody ?: "")
                        val errorMessage = errorResponse.optString("error", "Signup failed")
                        Toast.makeText(this@Signup, errorMessage, Toast.LENGTH_SHORT).show()
                    } catch (e: Exception) {
                        e.printStackTrace()
                        Toast.makeText(this@Signup, "Signup failed", Toast.LENGTH_SHORT).show()
                    }
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@Signup, "Network error: ${t.message}", Toast.LENGTH_SHORT)
                    .show()
            }
        })
    }

    private fun toggleOrgPasswordVisibility() {
        if (isOrgPasswordVisible) {
            orgPassEditText.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            orgPassEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyecnot, 0)
        } else {
            orgPassEditText.inputType = InputType.TYPE_CLASS_TEXT
            orgPassEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyec, 0)
        }
        isOrgPasswordVisible = !isOrgPasswordVisible
        orgPassEditText.setSelection(orgPassEditText.text.length)
    }


    private fun togglePasswordVisibility() {
        if (isPasswordVisible) {
            passwordEditText.inputType =
                InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(
                0,
                0,
                R.drawable.eyecnot,
                0
            )
        } else {
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(
                0,
                0,
                R.drawable.eyec,
                0
            )
        }
        isPasswordVisible = !isPasswordVisible
        passwordEditText.setSelection(passwordEditText.text.length)
    }
}