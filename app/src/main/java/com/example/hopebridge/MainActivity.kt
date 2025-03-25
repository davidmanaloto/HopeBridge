package com.example.hopebridge

import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.text.InputType
import android.view.MotionEvent
import android.view.View
import android.view.View.OnTouchListener
import android.view.animation.AnimationUtils
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST

interface ApiService {
    @FormUrlEncoded
    @POST("HopeBridge_Web/php/login.php") // User Login API
    fun userLogin(
        @Field("email") email: String,
        @Field("password") password: String
    ): Call<LoginResponse>

    @FormUrlEncoded
    @POST("HopeBridge_Web/php/org_login.php") // Organization Login API
    fun orgLogin(
        @Field("email") email: String,
        @Field("password") password: String
    ): Call<LoginResponse>
}


class MainActivity : AppCompatActivity() {
    private var backPressedTime: Long = 0
    private val backPressedDelay: Long = 3000 // 3 seconds
    private var isPasswordVisible = false
    private lateinit var apiService: ApiService
    private lateinit var sharedPref: SharedPreferences



    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_main)

        apiService = ApiClient.getRetrofitInstance().create(ApiService::class.java)
        sharedPref = getSharedPreferences("UserSession", MODE_PRIVATE)

        val userBtn = findViewById<Button>(R.id.userbtn)
        val orgBtn = findViewById<Button>(R.id.orgbtn)
        val userSignInLayout = findViewById<LinearLayout>(R.id.userSignInLayout)
        val orgSignInLayout = findViewById<LinearLayout>(R.id.orgSignInLayout)
        val registerBtn = findViewById<Button>(R.id.btnRegister)
        val orgRegisterbtn = findViewById<Button>(R.id.orgRegister)
        val passwordEditText = findViewById<EditText>(R.id.password)
        val orgPasswordEditText = findViewById<EditText>(R.id.orgPassword)
        val userEmail = findViewById<EditText>(R.id.email)
        val userPassword = findViewById<EditText>(R.id.password)
        val userLoginBtn = findViewById<Button>(R.id.btnSignIn)
        val orgEmail = findViewById<EditText>(R.id.orgEmail)
        val orgPassword = findViewById<EditText>(R.id.orgPassword)
        val orgLoginBtn = findViewById<Button>(R.id.orgSignIn)

        // Get drawables
        val defaultDrawable = ContextCompat.getDrawable(this, R.drawable.squarecorner)
        val selectedDrawable = ContextCompat.getDrawable(this, R.drawable.colorsquare)

        // Show user sign-in form by default
        userSignInLayout.visibility = View.VISIBLE
        orgSignInLayout.visibility = View.GONE
        userBtn.background = selectedDrawable

        // Load animations
        val fadeIn = AnimationUtils.loadAnimation(this, R.anim.fade_in)
        val fadeOut = AnimationUtils.loadAnimation(this, R.anim.fade_out)

        if (sharedPref.getBoolean("isLoggedIn", false)) {
            val userType = sharedPref.getString("type", "")
            if (userType == "user") {
                startActivity(Intent(this, Homepage::class.java))
            } else if (userType == "organization") {
                startActivity(Intent(this, OrgHomepage::class.java))
            }
            finish()
            checkLoginStatus()
        }


        userLoginBtn.setOnClickListener {
            val email = userEmail.text.toString().trim()
            val password = userPassword.text.toString().trim()
            loginUser(email, password)
        }

        orgLoginBtn.setOnClickListener {
            val email = orgEmail.text.toString().trim()
            val password = orgPassword.text.toString().trim()
            loginOrganization(email, password)
        }

        registerBtn.setOnClickListener {
            val intent = Intent(this, Signup::class.java)
            startActivity(intent)
        }

        orgRegisterbtn.setOnClickListener {
            val intent = Intent(this, Signup::class.java)
            startActivity(intent)
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


        val passwordToggleListener = OnTouchListener { v: View, event: MotionEvent ->
            if (event.action == MotionEvent.ACTION_UP) {
                val editText = v as EditText
                val drawableRight =
                    editText.compoundDrawablesRelative[2]
                if (drawableRight != null) {
                    val boundsWidth = drawableRight.bounds.width()
                    val drawableAreaStart =
                        editText.right - boundsWidth - editText.paddingRight
                    if (event.rawX >= drawableAreaStart) {
                        togglePasswordVisibility(editText)
                        return@OnTouchListener true
                    }
                }
            }
            false
        }

        passwordEditText.setOnTouchListener(passwordToggleListener)
        orgPasswordEditText.setOnTouchListener(passwordToggleListener)
    }

    private fun loginUser(email: String, password: String) {
        val call = apiService.userLogin(email, password)

        call.enqueue(object : Callback<LoginResponse> {
            override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>) {
                if (response.isSuccessful && response.body()?.status == "success") {
                    val userId = response.body()?.id
                    val username = response.body()?.username
                    val userType = "user" // or "organization" depending on the login

                    saveUserSession(userId, username, email, userType)

                    // Navigate to the home screen
                    startActivity(Intent(this@MainActivity, Homepage::class.java))
                    finish()
                } else {
                    Toast.makeText(this@MainActivity, "Invalid login credentials", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<LoginResponse>, t: Throwable) {
                Toast.makeText(this@MainActivity, "Login failed: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }


    private fun loginOrganization(email: String, password: String) {
        apiService.orgLogin(email, password).enqueue(object : Callback<LoginResponse> {
            override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>) {
                if (response.isSuccessful && response.body()?.status == "success") {
                    val org = response.body()
                    saveUserSession(org?.id, org?.username, org?.email, "organization")
                    Toast.makeText(applicationContext, "Organization Login Successful!", Toast.LENGTH_SHORT).show()
                    startActivity(Intent(this@MainActivity, OrgHomepage::class.java))
                } else {
                    Toast.makeText(applicationContext, "Invalid organization credentials", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<LoginResponse>, t: Throwable) {
                Toast.makeText(applicationContext, "Login Failed: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun checkLoginStatus() {
        val sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)
        val isLoggedIn = sharedPreferences.getBoolean("isLoggedIn", false)
        val userType = sharedPreferences.getString("user_type", "") // Corrected key from "type" to "user_type"

        if (isLoggedIn) {
            if (userType == "user") {
                startActivity(Intent(this, Homepage::class.java))
            } else if (userType == "organization") {
                startActivity(Intent(this, OrgHomepage::class.java))
            }
            finish()
        }
    }



    private fun saveUserSession(userId: Int?, username: String?, email: String?, userType: String) {
        val sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)
        val editor = sharedPreferences.edit()

        if (userType == "user") {
            editor.putInt("user_id", userId ?: -1)
        } else if (userType == "organization") {
            editor.putInt("organization_id", userId ?: -1)
        }

        editor.putString("username", username)
        editor.putString("email", email)
        editor.putString("user_type", userType)
        editor.putBoolean("isLoggedIn", true) // Ensure this flag is set
        editor.apply()
    }


    private fun togglePasswordVisibility(passwordEditText: EditText) {
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

    override fun onBackPressed() {
        if (backPressedTime + backPressedDelay > System.currentTimeMillis()) {
            super.onBackPressed()
        } else {
            toast("Press back again to close the app")
        }
        backPressedTime = System.currentTimeMillis()
    }

    private fun toast(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show()
    }


}
