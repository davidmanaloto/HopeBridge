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
import retrofit2.Response
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST

interface OrgLoginApi {
    @FormUrlEncoded
    @POST("HopeBridge_Web/php/org_login.php")
    fun orgLogin(
        @Field("organization_name") orgName: String,
        @Field("password") password: String
    ): Call<ResponseBody>
}

class SigninOrg : AppCompatActivity() {

    private lateinit var orgNameEditText: EditText
    private lateinit var passwordEditText: EditText
    private lateinit var signinButton: Button
    private lateinit var signUpButton: Button
    private var isPasswordVisible = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_signin_org)

        orgNameEditText = findViewById(R.id.orgusername)
        passwordEditText = findViewById(R.id.password)
        signinButton = findViewById(R.id.signin)
        signUpButton = findViewById(R.id.signup)

        signinButton.setOnClickListener {
            val orgName = orgNameEditText.text.toString().trim()
            val password = passwordEditText.text.toString().trim()

            if (orgName.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "Please enter organization name and password", Toast.LENGTH_SHORT).show()
            } else {
                signin(orgName, password)
            }
        }

        signUpButton.setOnClickListener {
            val intent = Intent(this, SignupOrg::class.java)
            startActivity(intent)
        }

        passwordEditText.setOnTouchListener { _, event ->
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
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyecnot, 0)
        } else {
            passwordEditText.inputType = InputType.TYPE_CLASS_TEXT
            passwordEditText.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyec, 0)
        }
        isPasswordVisible = !isPasswordVisible
        passwordEditText.setSelection(passwordEditText.text.length)
    }

    private fun signin(orgName: String, password: String) {
        val apiService = ApiClient.getRetrofitInstance().create(OrgLoginApi::class.java)
        val call = apiService.orgLogin(orgName, password)

        call.enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    response.body()?.string()?.let { jsonResponse ->
                        try {
                            val jsonObject = JSONObject(jsonResponse)
                            val status = jsonObject.optString("status", "")

                            if (status == "success") {
                                val orgId = jsonObject.optInt("organization_id", -1)
                                val sharedPreferences = getSharedPreferences("login_prefs", MODE_PRIVATE)
                                val editor = sharedPreferences.edit()
                                editor.putInt("organization_id", orgId)
                                editor.apply()

                                Toast.makeText(this@SigninOrg, "Login successful", Toast.LENGTH_SHORT).show()
                                startActivity(Intent(this@SigninOrg, OrgHomepage::class.java))
                                finish()
                            } else {
                                Toast.makeText(this@SigninOrg, "Login failed: ${jsonObject.optString("message")}", Toast.LENGTH_SHORT).show()
                            }
                        } catch (e: Exception) {
                            e.printStackTrace()
                        }
                    }
                } else {
                    Toast.makeText(this@SigninOrg, "Error: ${response.message()}", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@SigninOrg, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
