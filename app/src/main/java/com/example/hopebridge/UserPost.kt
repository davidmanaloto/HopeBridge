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
import android.widget.*
import androidx.activity.enableEdgeToEdge
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
        enableEdgeToEdge()
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

        profileSection.setOnClickListener { startActivity(Intent(this, User::class.java)) }
        logoutSection.setOnClickListener { logoutUser() }
        aboutSection.setOnClickListener { startActivity(Intent(this, About::class.java)) }
        orgsButton.setOnClickListener { startActivity(Intent(this, Organization::class.java)) }
        newsButton.setOnClickListener { startActivity(Intent(this, Homepage::class.java)) }

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
            val imageUri = imageUriString?.let { Uri.parse(it) }

            if (projectName != null && projectSummary != null) {
                addProjectToUI(projectName, projectSummary, imageUri)
            }

        }
    }


    private fun addProjectToUI(name: String, summary: String, imageUri: Uri?) {
        val projectView = LayoutInflater.from(this).inflate(R.layout.item_project, projectContainer, false)

        val projectNameTextView = projectView.findViewById<TextView>(R.id.projectNameTextView)
        val projectSummaryTextView = projectView.findViewById<TextView>(R.id.projectSummaryTextView)
        val projectImageView = projectView.findViewById<ImageView>(R.id.projectImageDisplay)

        projectNameTextView.text = name
        projectSummaryTextView.text = summary

        // Load Image with Glide
        if (imageUri != null) {
            Glide.with(this)
                .load(imageUri.toString()) // Convert Uri to String
                .into(projectImageView)
        }

        projectContainer.addView(projectView)
    }



    private fun fetchProjects() {
        val projectService = ApiClient.getRetrofitInstance().create(ProjectService::class.java)
        val call = projectService.fetchProjects()

        call.enqueue(object : Callback<List<Project>> {
            override fun onResponse(call: Call<List<Project>>, response: Response<List<Project>>) {
                if (response.isSuccessful) {
                    val projects = response.body() ?: emptyList()
                    projectContainer.removeAllViews() // Clear old projects

                    for (project in projects) {
                        Log.d("DEBUG", "Fetched Project: ${project.project_name}, Image: ${project.image_url}")
                        addProjectToUI(project.project_name, project.project_summary, project.image_url?.let { Uri.parse(it) })
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
