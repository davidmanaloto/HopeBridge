package com.example.hopebridge

import android.animation.ObjectAnimator
import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.Query

interface DonateService {
    @GET("HopeBridge_Web/project_php/fetch_completed_donations.php")
    fun getCompletedDonations(@Query("email") orgEmail: String): Call<List<Donation>>
}


class OrgHomepage : AppCompatActivity() {
    private lateinit var orgContainer: LinearLayout
    private lateinit var donateService: DonateService
    private var isMenuOpen = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_org_homepage)

        val hamburger: View = findViewById(R.id.hamburger)
        val burgers: View = findViewById(R.id.burgers)
        val profileSection: View = findViewById(R.id.profile_section)
        val logoutSection: View = findViewById(R.id.logout_section)
        val aboutSection: View = findViewById(R.id.about_section)
        val notif: ImageView = findViewById(R.id.notif)

        orgContainer = findViewById(R.id.OrganizationContainer)
        donateService = ApiClient.getRetrofitInstance().create(DonateService::class.java)

        burgers.translationX = -900f

        val sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)
        val orgEmail = sharedPreferences.getString("email", "")

        Log.d("OrgHomepage", "Retrieved Org Email: $orgEmail") // Debugging log

        if (!orgEmail.isNullOrEmpty()) {
            fetchCompletedDonations(orgEmail)
        } else {
            Toast.makeText(this, "No Organization Logged In", Toast.LENGTH_SHORT).show()
        }

        hamburger.setOnClickListener {
            val targetX = if (isMenuOpen) -900f else 0f
            ObjectAnimator.ofFloat(burgers, "translationX", targetX).setDuration(100).start()
            isMenuOpen = !isMenuOpen
        }

        notif.setOnClickListener {
            val intent = Intent(this, Notification::class.java)
            startActivity(intent)
        }

        profileSection.setOnClickListener {
            val intent = Intent(this, UserInfo::class.java)
            startActivity(intent)
        }

        aboutSection.setOnClickListener {
            val intent = Intent(this, About::class.java)
            startActivity(intent)
        }

        logoutSection.setOnClickListener {
            logoutUser()
        }
    }

    private fun fetchCompletedDonations(orgEmail: String) {
        donateService.getCompletedDonations(orgEmail).enqueue(object : Callback<List<Donation>> {
            override fun onResponse(call: Call<List<Donation>>, response: Response<List<Donation>>) {
                Log.d("OrgHomepage", "API Response: ${response.body()}") // Debugging log
                if (response.isSuccessful && response.body() != null) {
                    displayDonations(response.body()!!)
                } else {
                    Toast.makeText(this@OrgHomepage, "No Donations Found", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Donation>>, t: Throwable) {
                Log.e("OrgHomepage", "API Call Failed", t) // Debugging log
                Toast.makeText(this@OrgHomepage, "Failed to load donations", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun displayDonations(donations: List<Donation>) {
        orgContainer.removeAllViews()
        val inflater = LayoutInflater.from(this)

        for (donation in donations) {
            val view = inflater.inflate(R.layout.org_item, orgContainer, false)

            val donorName = view.findViewById<TextView>(R.id.donor_name)
            val projectName = view.findViewById<TextView>(R.id.project_name)
            val amountRaised = view.findViewById<TextView>(R.id.raised)
            val dateCreated = view.findViewById<TextView>(R.id.date_created)

            donorName.text = donation.username
            projectName.text = "Project: ${donation.projectName}"
            amountRaised.text = "Raised: ₱${donation.amount}"
            dateCreated.text = "Date: ${donation.dateCreated}"

            orgContainer.addView(view)
        }
    }

    private fun logoutUser() {
        val sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)
        val editor = sharedPreferences.edit()
        editor.clear()
        editor.apply()

        // Redirect to login screen
        startActivity(Intent(this, MainActivity::class.java))
        finish()
    }
}
