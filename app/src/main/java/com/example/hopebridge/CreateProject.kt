package com.example.hopebridge

import android.app.Activity
import android.content.Intent
import android.content.SharedPreferences
import android.net.Uri
import android.os.Bundle
import android.provider.MediaStore
import android.util.Log
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.http.DELETE
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Query

interface ProjectService {
    @FormUrlEncoded
    @POST("HopeBridge_Web/project_php/create_project.php")
    fun saveProject(
        @Field("user_id") userId: Int,
        @Field("project_name") projectName: String,
        @Field("project_summary") projectSummary: String,
        @Field("donation_goal") donationGoal: String,
        @Field("image_url") imageUrl: String?,
        @Field("organization_id") organizationId: Int
    ): Call<String>

    @FormUrlEncoded
    @POST("HopeBridge_Web/project_php/donate_project.php")
    fun saveDonation(
        @Field("user_id") userId: Int,
        @Field("project_name") projectName: String,
        @Field("amount") amount: Double
    ): Call<String>

    @GET("HopeBridge_Web/project_php/fetch_projects.php")
    fun fetchProjects(): Call<List<Project>>

    @GET("HopeBridge_Web/project_php/fetch_funds.php")
    fun getFundsRaised(@Query("project_name") projectName: String): Call<Project>

    @GET("HopeBridge_Web/php/fetch_organizations.php")
    fun fetchOrganizations(): Call<List<OrganizationData>>

    @DELETE("HopeBridge_Web/project_php/delete_project.php")
    fun deleteProject(@Query("project_name") projectName: String): Call<String>

}

class CreateProject : AppCompatActivity() {
    private lateinit var imageAdd: LinearLayout
    private lateinit var imageView: ImageView
    private var imageUri: Uri? = null
    private lateinit var nameEditText: EditText
    private lateinit var summaryEditText: EditText
    private lateinit var submitButton: Button
    private lateinit var organizationSpinner: Spinner
    private lateinit var sharedPreferences: SharedPreferences
    private var organizations: List<OrganizationData> = listOf()
    private val PICK_IMAGE_REQUEST = 1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_create_project)

        // Back button functionality
        val backbutton: ImageView = findViewById(R.id.back)
        backbutton.setOnClickListener {
            val intent = Intent(this, Homepage::class.java)
            startActivity(intent)
        }

        sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)

        // Initialize UI components
        organizationSpinner = findViewById(R.id.environmentSpinner)
        nameEditText = findViewById(R.id.name)
        summaryEditText = findViewById(R.id.summproject)
        submitButton = findViewById(R.id.submitButton)
        imageAdd = findViewById(R.id.imageadd)
        imageView = findViewById(R.id.imageIcon)

        // Fetch organizations from the database
        fetchOrganizations()

        // Image selection setup
        imageAdd.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI)
            startActivityForResult(intent, PICK_IMAGE_REQUEST)
        }

        submitButton.setOnClickListener {
            createProject()
        }
    }

    private fun fetchOrganizations() {
        val projectService = ApiClient.getRetrofitInstance().create(ProjectService::class.java)
        val call = projectService.fetchOrganizations()

        call.enqueue(object : Callback<List<OrganizationData>> {
            override fun onResponse(call: Call<List<OrganizationData>>, response: Response<List<OrganizationData>>) {
                if (response.isSuccessful && response.body() != null) {
                    organizations = response.body()!!

                    val organizationNames = mutableListOf("Please Select")
                    organizationNames.addAll(organizations.map { it.name })

                    val adapter = ArrayAdapter(this@CreateProject, android.R.layout.simple_spinner_item, organizationNames)
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    organizationSpinner.adapter = adapter

                }
            }

            override fun onFailure(call: Call<List<OrganizationData>>, t: Throwable) {
                Toast.makeText(this@CreateProject, "Failed to load organizations", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun createProject() {
        val projectName = nameEditText.text.toString().trim()
        val projectSummary = summaryEditText.text.toString().trim()
        val donationGoal = findViewById<EditText>(R.id.donategoal).text.toString().trim()
        val userId = sharedPreferences.getInt("user_id", -1)
        val selectedPosition = organizationSpinner.selectedItemPosition
        val organizationId = if (selectedPosition > 0) organizations[selectedPosition - 1].id else -1
        val imageUrl = imageUri?.toString() ?: ""

        if (projectName.isNotEmpty() && projectSummary.isNotEmpty() && donationGoal.isNotEmpty() && userId != -1 && organizationId != -1) {
            val projectService = ApiClient.getRetrofitInstance().create(ProjectService::class.java)
            val call = projectService.saveProject(userId, projectName, projectSummary, donationGoal, imageUrl, organizationId)

            call.enqueue(object : Callback<String> {
                override fun onResponse(call: Call<String>, response: Response<String>) {
                    if (response.isSuccessful) {
                        val projectId = response.body()?.toIntOrNull() ?: -1
                        if (projectId != -1) {
                            sharedPreferences.edit().putInt("project_id", projectId).apply()
                            sharedPreferences.edit().putInt("user_id", userId).apply()
                        }

                        Toast.makeText(this@CreateProject, "Project saved!", Toast.LENGTH_SHORT).show()
                        setResult(Activity.RESULT_OK, Intent().apply {
                            putExtra("projectName", projectName)
                            putExtra("projectSummary", projectSummary)
                            putExtra("imageUri", imageUrl)
                            putExtra("donationGoal", donationGoal)
                            putExtra("organizationId", organizationId)
                        })
                        finish()
                    } else {
                        Toast.makeText(this@CreateProject, "Failed to save", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<String>, t: Throwable) {
                    Toast.makeText(this@CreateProject, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        } else {
            Toast.makeText(this, "Please fill all fields and select an organization", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == PICK_IMAGE_REQUEST && resultCode == Activity.RESULT_OK && data != null) {
            imageUri = data.data
            imageView.setImageURI(imageUri)
            imageAdd.background = null

            val dragText: TextView = findViewById(R.id.dragText)
            dragText.visibility = View.GONE

            val layoutParams = imageView.layoutParams
            layoutParams.width = ViewGroup.LayoutParams.MATCH_PARENT
            layoutParams.height = 600
            imageView.layoutParams = layoutParams
        }
    }
}
