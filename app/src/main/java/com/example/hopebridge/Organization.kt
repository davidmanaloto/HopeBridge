package com.example.hopebridge

import android.animation.ObjectAnimator
import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.http.GET

interface OrganizationService {
    @GET("HopeBridge_Web/php/fetch_organizations.php") // Change this to your actual PHP file URL
    fun getOrganizations(): Call<List<OrganizationData>>
}

class Organization : AppCompatActivity() {

    private var isMenuOpen = false
    private lateinit var sharedPreferences: SharedPreferences
    private lateinit var organizationContainer: LinearLayout

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_organization)

        val hamburger: View = findViewById(R.id.hamburger)
        val burgers: View = findViewById(R.id.burgers)
        val profileSection: View = findViewById(R.id.profile_section)
        val logoutSection: View = findViewById(R.id.logout_section)
        val newsButton: Button = findViewById(R.id.news)
        val aboutSection: View = findViewById(R.id.about_section)
        val userpost: Button = findViewById(R.id.userpost)
        organizationContainer = findViewById(R.id.organizationContainer)

        sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)

        val username = sharedPreferences.getString("username", "Guest") ?: "Guest"
        findViewById<TextView>(R.id.welcome).text = "Welcome $username"

        burgers.translationX = -900f

        hamburger.setOnClickListener {
            val targetX = if (isMenuOpen) -900f else 0f
            ObjectAnimator.ofFloat(burgers, "translationX", targetX).setDuration(100).start()
            isMenuOpen = !isMenuOpen
        }

        profileSection.setOnClickListener {
            startActivity(Intent(this, UserInfo::class.java))
        }

        logoutSection.setOnClickListener {
            logoutUser()
        }

        newsButton.setOnClickListener {
            startActivity(Intent(this, Homepage::class.java))
        }

        aboutSection.setOnClickListener {
            startActivity(Intent(this, About::class.java))
        }

        userpost.setOnClickListener {
            startActivity(Intent(this, UserPost::class.java))
        }

        fetchOrganizations()
    }

    private fun fetchOrganizations() {
        val retrofit = ApiClient.getRetrofitInstance()
        val organizationService = retrofit.create(OrganizationService::class.java)

        val call = organizationService.getOrganizations()

        call.enqueue(object : Callback<List<OrganizationData>> {
            override fun onResponse(call: Call<List<OrganizationData>>, response: Response<List<OrganizationData>>) {
                if (response.isSuccessful) {
                    response.body()?.let { organizations ->
                        organizations.forEach { organization ->
                            addOrganizationToUI(organization.name, organization.tags, organization.description)
                        }
                    }
                } else {
                    Log.e("API_ERROR", "Error fetching organizations")
                }
            }

            override fun onFailure(call: Call<List<OrganizationData>>, t: Throwable) {
                Log.e("API_ERROR", "Failed to fetch organizations: ${t.message}")
            }
        })
    }

    private fun addOrganizationToUI(name: String, tags: String, description: String) {
        val organizationView = LayoutInflater.from(this).inflate(R.layout.organization_item, organizationContainer, false)

        val layoutParams = LinearLayout.LayoutParams(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )
        layoutParams.setMargins(0, 50, 0, 0) // Adds spacing
        organizationView.layoutParams = layoutParams

        val organizationNameTextView = organizationView.findViewById<TextView>(R.id.org_name)
        val organizationTagsTextView = organizationView.findViewById<TextView>(R.id.org_tags)
        val organizationDescriptionTextView = organizationView.findViewById<TextView>(R.id.org_description)

        organizationNameTextView.text = name
        organizationTagsTextView.text = tags
        organizationDescriptionTextView.text = description

        organizationContainer.addView(organizationView)
    }

    private fun logoutUser() {
        val editor = sharedPreferences.edit()
        editor.clear()
        editor.apply()

        val intent = Intent(this, MainActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }
}
