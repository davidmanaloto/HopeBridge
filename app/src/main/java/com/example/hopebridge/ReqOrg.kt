package com.example.hopebridge

import android.content.Intent
import android.os.Bundle
import android.text.InputType
import android.view.MotionEvent
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import okhttp3.ResponseBody
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.http.*

interface OrgApiService {
    @FormUrlEncoded
    @POST("HopeBridge_Web/php/org_signup.php")
    fun signupOrg(
        @Field("email") email: String,
        @Field("organization_name") organizationName: String,
        @Field("password") password: String,
        @Field("contact_number") contactNumber: String,
        @Field("address") address: String,
        @Field("verification_reason") verificationReason: String,
        @Field("verification_document") verificationDocument: String // File path if needed
    ): Call<ResponseBody>
}

class ReqOrg : AppCompatActivity() {

    private lateinit var orgPassField: EditText
    private var isOrgPasswordVisible = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_req_org)

        orgPassField = findViewById(R.id.Orgpass)

        val backButton: ImageView = findViewById(R.id.back)
        val submitButton: Button = findViewById(R.id.submitButton)

        val orgEmailField = findViewById<EditText>(R.id.OrgEmail)
        val orgNameField = findViewById<EditText>(R.id.Orgname)
        val orgPassField = findViewById<EditText>(R.id.Orgpass)
        val contactField = findViewById<EditText>(R.id.contactnumb)
        val addressField = findViewById<EditText>(R.id.orgAddress)
        val reasonField = findViewById<EditText>(R.id.orgReason)

        val orgEmail = intent.getStringExtra("orgEmail")
        val orgName = intent.getStringExtra("orgName")
        val orgPass = intent.getStringExtra("orgPass") // Receiving password


        // Set received data
        orgEmailField.setText(orgEmail)
        orgNameField.setText(orgName)
        orgPassField.setText(orgPass)


        backButton.setOnClickListener {
            val intent = Intent(this@ReqOrg, Signup::class.java)
            startActivity(intent)
        }

        orgPassField.setOnTouchListener { _, event ->
            if (event.action == MotionEvent.ACTION_UP) {
                val drawableRight = orgPassField.compoundDrawablesRelative[2]
                if (drawableRight != null) {
                    val boundsWidth = drawableRight.bounds.width()
                    val drawableAreaStart = orgPassField.right - boundsWidth - orgPassField.paddingRight
                    if (event.rawX >= drawableAreaStart) {
                        toggleOrgPasswordVisibility()
                        return@setOnTouchListener true
                    }
                }
            }
            false
        }


        submitButton.setOnClickListener {
            val orgEmail = orgEmailField.text.toString().trim()
            val orgName = orgNameField.text.toString().trim()
            val orgPass = orgPassField.text.toString().trim()
            val contact = contactField.text.toString().trim()
            val address = addressField.text.toString().trim()
            val reason = reasonField.text.toString().trim()
            val document = "" // Placeholder for document handling

            if (orgEmail.isEmpty() || orgName.isEmpty() || orgPass.isEmpty() ||
                contact.isEmpty() || address.isEmpty() || reason.isEmpty()) {
                Toast.makeText(this, "All fields are required!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            registerOrg(orgEmail, orgName, orgPass, contact, address, reason, document)
        }
    }

    private fun registerOrg(
        email: String, orgName: String, password: String,
        contact: String, address: String, reason: String, document: String
    ) {
        val apiService = ApiClient.getRetrofitInstance().create(OrgApiService::class.java)
        val call = apiService.signupOrg(email, orgName, password, contact, address, reason, document)

        call.enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    val responseBody = response.body()?.string()
                    val jsonResponse = JSONObject(responseBody ?: "")

                    if (jsonResponse.has("message")) {
                        Toast.makeText(this@ReqOrg, jsonResponse.getString("message"), Toast.LENGTH_SHORT).show()
                        startActivity(Intent(this@ReqOrg, MainActivity::class.java))
                        finish()
                    } else {
                        Toast.makeText(this@ReqOrg, "Unexpected response", Toast.LENGTH_SHORT).show()
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    val errorResponse = JSONObject(errorBody ?: "")
                    val errorMessage = errorResponse.optString("error", "Signup failed")
                    Toast.makeText(this@ReqOrg, errorMessage, Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@ReqOrg, "Network error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun toggleOrgPasswordVisibility() {
        if (isOrgPasswordVisible) {
            orgPassField.inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
            orgPassField.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyecnot, 0)
        } else {
            orgPassField.inputType = InputType.TYPE_CLASS_TEXT
            orgPassField.setCompoundDrawablesRelativeWithIntrinsicBounds(0, 0, R.drawable.eyec, 0)
        }
        isOrgPasswordVisible = !isOrgPasswordVisible
        orgPassField.setSelection(orgPassField.text.length)
    }

}
