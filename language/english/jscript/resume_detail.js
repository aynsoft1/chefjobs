$(document).ready(function () {
  const ids = $(".resumeLeftBox").attr("id");
  resume_box(ids);
});

function resume_box(id) {
  console.log("selected id is ", id);

  $.ajax({
    url: `api/resume_search.php?resumeId=${id}`,
    method: "GET",
    dataType: "JSON",
    success: function (result) {
      if (result.length > 0) {
        const resumeData = result[0];

        // console.log("resume data for id is: ", resumeData);

        const {
          user_name,
          job_designation,
          user_email,
          phone,
          address,
          rating,
          profile_img,
        } = resumeData.user_profile;

        const { target_job, job_type, job_category, salary, relocate } =
          resumeData.job_info;

        // const { link, fileName } = resumeData.resumeDownloadLink !== null ? resumeData.resumeDownloadLink : null;
        const link = resumeData.resumeDownloadLink !== null ? resumeData.resumeDownloadLink.link : null
        const fileName = resumeData.resumeDownloadLink !== null ? resumeData.resumeDownloadLink.fileName : null


        const { facebook_url, google_url, twitter_url, linkedin_url } =
          resumeData.social_link;

        const workExp = resumeData.work_history;
        let workExperience = "";
        jQuery.each(
          workExp,
          function (
            i,
            { company, job_title, job_category, job_location, working_date }
          ) {
            workExperience += `
            <div class="col-md-6 mb-4">
                <div class="fw-bold mt-3">${company}</div>
                <div>${
                  job_title ? `<i class="bi bi-person me-2"></i> ${job_title}` : ''
                }</div>
                <div class="">${
                  job_category ?
                  `<i class="bi bi-briefcase me-2"></i> ${job_category}` : ''
                }</div>
                <div class="">${
                  job_location ?
                  `<i class="bi bi-geo-alt me-2"></i> ${job_location}` : ''
                }</div>
                <div class="">${
                  working_date ?
                  `<i class="bi bi-clock me-2"></i> ${working_date}` : ''
                }</div>
                <div class=""></div>
            </div>
            `;
          }
        );

        const education = resumeData.education;
        let educationDetail = "";
        jQuery.each(
          education,
          function (i, { specializtion, school, country, date, info }) {
            educationDetail += `
            <div class="mb-4">
                <div class="fw-bold mt-3">${specializtion}</div>
                <div>${
                  school ? `<i class="bi bi-mortarboard me-2"></i> ${school}` : ''
                }</div>
                <div class="small">${
                  country ? `<i class="bi bi-geo-alt me-2"></i> ${country}` : ''
                }</div>
                <div class="small">${
                  date ? `<i class="bi bi-clock me-2"></i> ${date}` : ''
                }</div>
                <div class="small">${
                  info ? `<i class="bi bi-info-circle me-2"></i> ${info}` : ''
                }</div>
            </div>
            `;
          }
        );

        const skills = resumeData.skills;
        let userSkills = "";
        jQuery.each(
          skills,
          function (i, { skill, skill_level, last_used, year_of_exp }) {
            userSkills += `
            <div class="col-md-4 mb-4">
                <div class="fw-bold mt-3">${
                  skill ? `<i class="bi bi-code-slash me-2"></i> ${skill}` : ''
                }</div>
                <div class="small">${
                  skill_level ?
                  `<i class="bi bi-sliders me-2"></i> ${skill_level}` : ''
                }</div>
                <div class="small">${
                  last_used ?
                  `<i class="bi bi-clock-history me-2"></i> ${last_used}` : ''
                }</div>
                <div class="small">${
                  year_of_exp ?
                  `<i class="bi bi-briefcase me-2"></i> ${year_of_exp}` : ''
                }</div>
            </div>
            `;
          }
        );

        const references = resumeData.references;
        let userReferences = "";
        jQuery.each(
          references,
          function (
            i,
            {
              name,
              company_name,
              country,
              position,
              email,
              contact_no,
              relationship,
            }
          ) {
            userReferences += `
            <div class="col-md-6 mb-4">
                <div class="fw-bold mt-3">${
                  name ? `<i class="bi bi-person me-2"></i> ${name}` : ''
                }</div>
                <div>${
                  company_name ?
                  `<i class="bi bi-building me-2"></i> ${company_name}` : ''
                }</div>
                <div>${
                  country ? `<i class="bi bi-geo-alt me-2"></i> ${country}` : ''
                }</div>
                <div>${
                  position ? `<i class="bi bi-briefcase me-2"></i> ${position}` : ''
                }</div>
                <div>${
                  email ? `<i class="bi bi-envelope me-2"></i> ${email}` : ''
                }</div>
                <div>${
                  contact_no ?
                  `<i class="bi bi-telephone me-2"></i> ${contact_no}` : ''
                }</div>
                <div>${
                  relationship ?
                  `<i class="bi bi-person-circle me-2"></i> ${relationship}` : ''
                }</div>
            </div>
            `;
          }
        );

        const languages = resumeData.languages;
        let userKnowLang = "";
        jQuery.each(languages, function (i, { language, proficiency }) {
          userKnowLang += `
            <div class="d-flex align-items-center mt-2">
                <div class="fw-bold badge"
                    style="width:110px;color: #000!important;background-color: #eee;padding: 9px 0;">
                    ${language ? language : ''}</div>
                <div class=" text-center" style="width:60px;"><i class="bi bi-arrow-right"></i>
                </div>
                <div class="d-flex">${proficiency ? proficiency : ''}</div>
            </div>
            `;
        });

        // console.log(resumeData.pasted_resume[0].cvs)
        var cardDiv = `
        <div id="sticky-anchor"></div>
			<section class="two-pane-serp-page__detail-view for-mobile" style="height: calc(100vh - 69px);">
				<div class="card card-custom" id="sticky">
					<div class="card-header card-header-custom resume-bg-result">&nbsp;</div>
					<div class="card-body pt-4 px-4">
						<div class="d-flex" style="margin-top:-210px;">
							<div class="flex-shrink-0">
								<div class="resume-pic">
									<img class="img-fluid border" id="userProfileImg" src="${profile_img}">
								</div>
							</div>
							<div class="flex-grow-1 ms-3">
								<div class="">
									<h4 class="m-0 text-capitalize" style="font-size:26px;">
                                        <a href="${resumeData.view_resume_page_link}" target="_new" rel="noopener noreferrer">
                                            ${user_name}
                                        </a>
                                    </h4>
									<div class="cname">${
                    job_designation
                      ? '<i class="bi bi-person me-2"></i>' + job_designation
                      : ""
                  }
									</div>
									<div class="location">${
                    user_email
                      ? '<i class="bi bi-at me-2"></i> ' + user_email
                      : ""
                  }</div>
									<div class="location">${
                    phone && phone
                  }</div>
									<div class="location">${
                    address && '<i class="bi bi-geo-alt me-2"></i> ' + address
                  }</div>
									<div class="location">
                                        ${
                                          rating &&
                                          '<i class="bi bi-star me-2"></i> Current rating : ' +
                                            rating
                                        } 
                                        <span class="ms-3">
                                            <a href="${resumeData.rate_resume_main}" style="background: #eee;border-radius: 50px;padding: 2px 10px;font-size: 13px;">
                                                Rate now!
                                            </a>
                                        </span>
                                    </div>
									<ul class="list-group list-group-horizontal d-flex align-items-center m-0 mt-2">
										<li class="list-group-item list-group-item-custom" style="margin-right: 0;">
											<a class="btn download-btn me-3" title="Connect to job" href="${resumeData.connect_link}"
												title="Apply">
                                                Connect to job</a>
										</li>
										<li class="list-group-item list-group-item-custom">
											<a class="" href="${resumeData.download_resume}" title="Download Resume"><span
													style="font-size:22px!important;"
													class="icon-unsaved mobile-absolute-right"><i
														class="bi bi-box-arrow-down"></i></span></a>
										</li>
										<li class="list-group-item list-group-item-custom">
											<a class="" href="${resumeData.contact_person}" title="Contact this person"><span
													style="font-size:22px!important;"
													class="icon-unsaved mobile-absolute-right"><i
														class="bi bi-envelope"></i></span></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
						<!-- Basic Info and Social Profile Starts -->
						<div class="row">
							<div class="col-md-6">
								<h3 class="mb-3" style="font-size:20px;font-weight:bold;">${HEADING_BASIC_INFO}</h3>
								<div class="table-responsive-sm">
                                    ${
                                      target_job ?
                                      `
                                        <div>
                                            <div class="cname">Target Job </div>
                                            <div><span class="location">${target_job}</span></div>
                                        </div>
                                        ` : ''
                                    }

                                    ${
                                      job_type ?
                                      `
                                        <div>
                                            <div class="cname mt-2">Job Type </div>
                                            <div><span class="location"> ${job_type}</span>
                                            </div>
                                        </div>
                                        ` : ''
                                    }

                                    ${
                                      job_category ?
                                      `
                                        <div>
                                            <div class="cname mt-2">Job Category </div>
                                            <div><span class="location"> ${job_category}</span></div>
                                        </div>
                                        ` : ''
                                    }

                                    ${
                                      salary ?
                                      `
                                        <div>
                                            <div class="cname mt-2">Desired Salary/Wage </div>
                                            <div><span class="location"> ${salary}</span></div>
                                        </div>
                                        ` : ''
                                    }

                                    ${
                                      relocate ?
                                      `
                                        <div>
                                            <div class="cname mt-2">Willing to relocate </div>
                                            <div><span class="location"> ${relocate}</span></div>
                                        </div>
                                        ` : ''
                                    }

                                    ${
                                      link ?
                                      `
                                        <div>
                                            <div class="cname mt-2" "="">Attached Resume</div>
                                            <div class="location">
                                                <a href="${link}">
                                                ${fileName}
                                                </a>
                                            </div>
                                        </div>
                                        ` : ''
                                    }
								</div>
							</div>
							<div class="col-md-6">
                                ${
                                  (facebook_url ||
                                    linkedin_url ||
                                    google_url ||
                                    twitter_url) ?
                                  `
								<h3 class="mb-3" style="font-size:20px;font-weight:bold;">Social Profile</h3>
								<div class="table-responsive-sm">
                                    ${
                                      facebook_url ?
                                      `
                                        <div class="mb-3">
                                            <div class="cname">Facebook</div>
                                            <div><a class="location" href="${facebook_url}"><i
                                                        class="bi bi-facebook me-2 facebook"></i>${facebook_url}</a>
                                            </div>
                                        </div>
                                    ` : ''
                                    }
                                    ${
                                      google_url ?
                                      `
                                        <div class="mb-3">
                                            <div class="cname">Google</div>
                                            <div><a class="location" href="${google_url}"><i
                                                        class="bi bi-google me-2 google"></i>${google_url}</a>
                                            </div>
                                        </div>
                                    ` : ''
                                    }
                                    ${
                                      linkedin_url ?
                                      `
                                        <div class="mb-3">
                                            <div class="cname">Linkedin</div>
                                            <div><a class="location" href="${linkedin_url}"><i
                                                        class="bi bi-linkedin me-2 linkedin"></i>${linkedin_url}</a>
                                            </div>
                                        </div>
                                    `: ''
                                    }
                                    ${
                                      twitter_url ?
                                      `
                                        <div class="mb-3">
                                            <div class="cname">Twitter</div>
                                            <div><a class="location" href="${twitter_url}"><i
                                                        class="bi bi-twitter me-2 twitter"></i>${twitter_url}</a>
                                            </div>
                                        </div>
                                    `: ''
                                    }
								</div>
                                `:''
                                }
							</div>
							<!-- Basic Info and Social Profile Ends -->
						</div>
                        
						<!-- Objective Starts -->
                        ${
                          resumeData.objective ?
                          `
						<hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
						<div>
							<h3 class="mb-2" style="font-size:20px;font-weight:bold;">${HEADING_Objective}</h3>
							<div class="m-0 p-0">${resumeData.objective}</div>
						</div>
                        `: ''
                        }
                        
                        
						<!-- Work History Starts -->
                        ${
                            resumeData.work_history.length > 0 ?
                          `
                            <hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
                            <div class="">
                                <h3 class="resume-heading" style="font-size:20px;font-weight:bold;">${HEADING_WORK_HISTORY}</h3>
                                <div class="ca">
                                    ${
                                      resumeData.total_experience ?
                                      `
                                        <div>${resumeData.total_experience}</div>
                                    ` : ''
                                    }
                                    <div class="row">
                                        ${workExperience}
                                    </div>
                                </div>
                            </div>
                        ` : ''
                        }
						<!-- Work History Ends -->

                        
						<!-- Education Section Starts -->
                        ${
                          resumeData.education.length > 0 ?
                          `
                        <hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
						<div>
                            <div class="mb-2" style="font-size:20px;font-weight:bold;">${HEADING_EDUCATION}</div>
                            ${educationDetail}
                        </div>
                        ` : ''
                        }
						<!-- Education Section Ends -->

                        
						<!-- Skills Section Starts -->
                        ${
                          resumeData.skills.length > 0 ?
                          `
						<hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
						<div>
							<div class="mb-2 resume-heading" style="font-size:20px;font-weight:bold;">${HEADING_SKILLS}</div>
							<div class="row">
								${userSkills}
							</div>
						</div>
                        ` : ''
                        }
						<!-- Skills Section Ends -->

                        
						<!-- Refference Section Starts -->
                        ${
                            resumeData.references.length > 0 ?
                        `
                        <hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
                        <div class="">
                            <h3 class="resume-heading" style="font-size:20px;font-weight:bold;">Reference Details</h3>
                            <div class="row">
                                    ${userReferences}
                            </div>
                        </div>
                        ` : ''
                        }
						<!-- Refference Section Ends -->

						<!-- Languages Section Starts -->
                        ${
                          resumeData.languages.length > 0 ?
                          `
						<hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
						<div class="">
							<h3 class="resume-heading" style="font-size:20px;font-weight:bold;">${HEADING_LANGUAGES}</h3>
							<div class="table-responsive-sm">
								${userKnowLang}
							</div>
						</div>
                        `: ''
                        }
						<!-- Languages Section Ends -->

						<!-- Video Resume Starts -->
                        ${
                          resumeData.video_resume ?
                          `
						<hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
						<div class="">
							<h3 class="resume-heading" style="font-size:20px;font-weight:bold;">Video Resume</h3>
							<div class="resume-video"><iframe width="560" height="315" class="video"
									src="${resumeData.video_resume}"
									scrolling="no" frameborder="0"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
									allowfullscreen="" noscrollbars=""></iframe></div>
						</div>
                        `: ''
                        }
						<!-- Video Resume Ends -->

                        
						<!-- Pasted Resume Starts -->
                        ${
                            resumeData.pasted_resume[0].cvs !== null ?
                          `
                            <hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
                            <div class="mb-5">
                                <h3 class="resume-heading" style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                                    ${HEADING_PASTED}</h3>
                                <div class="mb-5">
                                    ${resumeData.pasted_resume[0].cvs}
                                </div>
                            </div>
                            `:''
                        }
						<!-- Pasted Resume Ends -->

					</div>
				</div>
			</section>
        `;

        $("#resumePreviewBox").html(cardDiv);
      } else {
        console.log("Data not found for id " + id);
      }
    },
    error: function (err) {
      console.log("something went wrong");
    },
  });
}
