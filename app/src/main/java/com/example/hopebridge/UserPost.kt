package com.example.hopebridge

import android.animation.ObjectAnimator
import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.net.Uri
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import com.bumptech.glide.Glide
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response




class UserPost : AppCompatActivity() {

    private var isMenuOpen = false
    private lateinit var sharedPreferences: SharedPreferences
    private lateinit var projectContainer: LinearLayout
    private val CREATE_PROJECT_REQUEST = 1001

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_user_post)

        val hamburger: View = findViewById(R.id.hamburger)
        val burgers: View = findViewById(R.id.burgers)
        val profileSection: View = findViewById(R.id.profile_section)
        val logoutSection: View = findViewById(R.id.logout_section)
        val aboutSection: View = findViewById(R.id.about_section)
        val createProject: Button = findViewById(R.id.Createproj)
        val orgsButton: Button = findViewById(R.id.orgs)
        val newsButton: Button = findViewById(R.id.news)
        projectContainer = findViewById(R.id.projectContainer)

        burgers.translationX = -900f

        sharedPreferences = getSharedPreferences("userPrefs", MODE_PRIVATE)
        val username = sharedPreferences.getString("username", "Guest") ?: "Guest"
        findViewById<TextView>(R.id.welcome).text = "Welcome $username"

        hamburger.setOnClickListener {
            val targetX = if (isMenuOpen) -900f else 0f
            ObjectAnimator.ofFloat(burgers, "translationX", targetX).setDuration(100).start()
            isMenuOpen = !isMenuOpen
        }

        profileSection.setOnClickListener {
            val intent = Intent(this, User::class.java)
            startActivity(intent)
        }

        logoutSection.setOnClickListener {
            logoutUser()
        }

        orgsButton.setOnClickListener {
            val intent = Intent(this, Organization::class.java)
            startActivity(intent)
        }

        aboutSection.setOnClickListener {
            val intent = Intent(this, About::class.java)
            startActivity(intent)
        }

        newsButton.setOnClickListener {
            startActivity(Intent(this, Homepage::class.java)) }

        createProject.setOnClickListener {
            val intent = Intent(this, CreateProject::class.java)
            startActivityForResult(intent, CREATE_PROJECT_REQUEST)
        }

        fetchProjects()
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == CREATE_PROJECT_REQUEST && resultCode == Activity.RESULT_OK && data != null) {
            val projectName = data.getStringExtra("projectName")
            val projectSummary = data.getStringExtra("projectSummary")
            val imageUriString = data.getStringExtra("imageUri")
            val donationGoal = data.getStringExtra("donationGoal") // Retrieve donation goal
            val imageUri = imageUriString?.let { Uri.parse(it) }

            if (projectName != null && projectSummary != null && donationGoal != null) {
                addProjectToUI(projectName, projectSummary, imageUri, donationGoal)
            }
        }
    }

    private fun addProjectToUI(name: String, summary: String, imageUri: Uri?, donationGoal: String?) {
        val projectView = LayoutInflater.from(this).inflate(R.layout.item_project, projectContainer, false)

        val layoutParams = LinearLayout.LayoutParams(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )
        layoutParams.setMargins(0, 50, 0, 0) // Adds spacing at the top
        projectView.layoutParams = layoutParams

        val projectNameTextView = projectView.findViewById<TextView>(R.id.projectNameTextView)
        val projectSummaryTextView = projectView.findViewById<TextView>(R.id.projectSummaryTextView)
        val seeMoreButton = projectView.findViewById<TextView>(R.id.seeMoreButton) // Add this in XML
        val projectImageView = projectView.findViewById<ImageView>(R.id.projectImageDisplay)
        val fundGoalTextView = projectView.findViewById<TextView>(R.id.fundgoal)
        val numberFundTextView = projectView.findViewById<TextView>(R.id.numberfund)
        val donateBtn = projectView.findViewById<Button>(R.id.donatebtn)

        projectNameTextView.text = name
        fundGoalTextView.text = "Goal: $$donationGoal"

        val maxSummaryLength = 100
        if (summary.length > maxSummaryLength) {
            projectSummaryTextView.text = summary.substring(0, maxSummaryLength) + "..."
            seeMoreButton.visibility = View.VISIBLE
        } else {
            projectSummaryTextView.text = summary
            seeMoreButton.visibility = View.GONE
        }

        seeMoreButton.setOnClickListener {
            if (seeMoreButton.text == "See More") {
                projectSummaryTextView.text = summary
                seeMoreButton.text = "See Less"
            } else {
                projectSummaryTextView.text = summary.substring(0, maxSummaryLength) + "..."
                seeMoreButton.text = "See More"
            }
        }

        if (imageUri != null) {
            Glide.with(this)
                .load(imageUri.toString())
                .into(projectImageView)
        }

        fetchFundsRaised(name, numberFundTextView)

        donateBtn.setOnClickListener {
            showDonateDialog(name)
        }

        projectContainer.addView(projectView)
    }


    fun fetchFundsRaised(projectName: String, textView: TextView) {
        val retrofit = ApiClient.getRetrofitInstance()
        val projectService = retrofit.create(ProjectService::class.java)

        val call = projectService.getFundsRaised(projectName)

        call.enqueue(object : Callback<Project> {
            override fun onResponse(call: Call<Project>, response: Response<Project>) {
                if (response.isSuccessful) {
                    val fundsResponse = response.body()
                    fundsResponse?.let {
                        textView.text = "Funds Raised: $${it.funds_raised}"
                    } ?: run {
                        textView.text = "Funds Raised: $0"
                    }
                } else {
                    textView.text = "Error fetching funds"
                }
            }

            override fun onFailure(call: Call<Project>, t: Throwable) {
                textView.text = "Failed to fetch funds"
                Log.e("API_ERROR", "Error: ${t.message}")
            }
        })
    }


    private fun showDonateDialog(projectName: String) {
        val dialogView = LayoutInflater.from(this).inflate(R.layout.dialog_donate, null)
        val builder = android.app.AlertDialog.Builder(this)
            .setView(dialogView)
            .setCancelable(true)

        val dialog = builder.create()
        dialog.show()

        val donateAmountEditText = dialogView.findViewById<EditText>(R.id.donateAmount)
        val submitButton = dialogView.findViewById<Button>(R.id.donateNowButton)

        submitButton.setOnClickListener {
            val amount = donateAmountEditText.text.toString()
            if (amount.isNotEmpty()) {
                saveDonationToDatabase(projectName, amount.toDouble())
                dialog.dismiss()
            } else {
                Toast.makeText(this, "Please enter an amount", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun saveDonationToDatabase(projectName: String, amount: Double) {
        val projectService = ApiClient.getRetrofitInstance().create(ProjectService::class.java)

        val call = projectService.saveDonation(projectName, amount)
        call.enqueue(object : Callback<String> {
            override fun onResponse(call: Call<String>, response: Response<String>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@UserPost, "Donated to $projectName" , Toast.LENGTH_SHORT).show()
                    fetchProjects() // Refresh project UI
                } else {
                    Toast.makeText(this@UserPost, "Failed to save donation", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<String>, t: Throwable) {
                Toast.makeText(this@UserPost, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun fetchProjects() {
        val projectService = ApiClient.getRetrofitInstance().create(ProjectService::class.java)
        val call = projectService.fetchProjects()

        call.enqueue(object : Callback<List<Project>> {
            override fun onResponse(call: Call<List<Project>>, response: Response<List<Project>>) {
                if (response.isSuccessful) {
                    val projects = response.body() ?: emptyList()
                    projectContainer.removeAllViews()

                    for (project in projects) {
                        Log.d("DEBUG", "Fetched Project: ${project.projectName}, Image: ${project.imageUrl}, Goal: ${project.donationGoal}")
                        addProjectToUI(
                            project.projectName,
                            project.projectSummary,
                            project.imageUrl?.let { Uri.parse(it) },
                            project.donationGoal // ✅ Pass donation goal
                        )
                    }
                } else {
                    Toast.makeText(this@UserPost, "Failed to load projects", Toast.LENGTH_SHORT).show()
                }
            }
            override fun onFailure(call: Call<List<Project>>, t: Throwable) {
                Toast.makeText(this@UserPost, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                Log.e("ERROR", "Fetch projects failed: ${t.message}")
            }
        })
    }


    private fun logoutUser() {
        sharedPreferences.edit().clear().apply()
        val intent = Intent(this, MainActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }
}
