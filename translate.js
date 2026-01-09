// Complete Translation System for PT Indo OceanCrew Services

class TranslationSystem {
  constructor() {
    this.currentLang = localStorage.getItem("preferredLanguage") || "en";
    this.translations = this.getTranslations();
    this.initialize();
  }

  getTranslations() {
    return {
      en: {
        // Meta & Page Titles
        title:
          "PT Indo OceanCrew Services - Professional Crewing Agency & Ship Chandler",
        tagline: "Professional Maritime Solutions",

        // Navigation
        nav: {
          home: "Home",
          about: "About Us",
          services: "Services",
          departments: "Crewing",
          contact: "Contact",
          contactBtn: "Contact Us",
          crewing: "Crewing Agency",
          chandler: "Ship Chandler",
          documentation: "Vessel Documentation",
          logistics: "Maritime Logistics",
          crew: "Our Crew",
        },

        // Hero Section
        hero: {
          subtitle: "Professional Crewing Agency & Ship Chandler Services",
          title:
            "Maritime Excellence <span class='highlight'>Across Oceans</span>",
          description:
            "Trusted maritime service provider specializing in crew management and ship supply solutions. Ensuring every vessel operates smoothly, safely, and efficiently.",
          servicesBtn: "Our Services",
          contactBtn: "Get Quote",
          subtitle2: "Global Maritime Solutions",
          description2:
            "Comprehensive maritime services meeting international standards (MLC, STCW, ISM Code) with integrity, reliability, and excellence.",
        },

        // Services Highlights
        services: {
          highlight1: {
            title: "Crewing Agency Services",
            desc: "Professional crew management and recruitment solutions for all vessel types",
          },
          highlight2: {
            title: "Ship Chandler Services",
            desc: "Quality marine provisions and supply solutions for global fleets",
          },
          highlight3: {
            title: "Vessel Documentation",
            desc: "Compliance support with international maritime regulations",
          },
          learnMore: "Learn More",
          crewing: {
            subtitle: "Professional Crew Management",
            title: "Crewing Agency <span class='highlight'>Services</span>",
            description:
              "Comprehensive crew management solutions for all vessel types, ensuring qualified and certified seafarers for safe and efficient maritime operations.",
            applyNow: "Apply Now",
            requirements: "Requirements:",
            certificates: {
              master: "Master License",
              stcw: "STCW",
              gmdss: "GMDSS",
              chiefMate: "Chief Mate License",
              bulk: "Bulk Cargo",
              oow: "OOW License",
              ecdis: "ECDIS",
              ff: "Firefighting",
              ab: "AB Certificate",
              pscrb: "PSCRB",
              chiefEng: "Chief Engineer License",
              management: "Management Level",
              secondEng: "Second Engineer License",
              maintenance: "Maintenance",
              oowEng: "OOW (Engine) License",
              electrical: "Electrical",
              basic: "Basic Training",
              erRating: "ER Rating Certificate",
              welding: "Welding",
              hospitality: "Hospitality Management",
              foodSafety: "Food Safety",
              leadership: "Leadership",
              culinary: "Culinary Arts",
              haccp: "HACCP",
              fnb: "F&B Management",
              wine: "Wine Service",
              service: "Service Excellence",
              housekeeping: "Housekeeping",
              cleaning: "Cleaning Procedures",
              supervisory: "Supervisory",
              entertainment: "Entertainment Management",
              mc: "Master of Ceremonies",
              performing: "Performing Arts",
              music: "Music/Dance",
              acting: "Acting",
              recreation: "Recreation Management",
              event: "Event Planning",
              firstAid: "First Aid",
              childcare: "Childcare",
              education: "Education",
            },
            nav: {
              deck: "Deck Department",
              engine: "Engine Department",
              hotel: "Hotel Department",
              entertainment: "Entertainment Department",
              recruitment: "Recruitment Process",
            },
            deck: {
              subtitle: "Navigation & Operations",
              title: "Deck <span class='highlight'>Department</span>",
              description:
                "Professional deck officers and crew responsible for navigation, cargo operations, and vessel safety.",
              positions: {
                master: {
                  rank: "Master / Captain",
                  salary: "$5,000 - $12,000",
                  title: "Master (Captain)",
                  description:
                    "Overall command of the vessel, responsible for safety, navigation, and compliance with international regulations.",
                  requirement1: "Master Mariner License (Unlimited)",
                  requirement2: "Minimum 5 years as Chief Officer",
                  requirement3: "STCW certifications",
                  requirement4: "Valid medical certificate",
                },
                chiefOfficer: {
                  rank: "Chief Officer",
                  salary: "$4,000 - $8,000",
                  title: "Chief Officer (First Mate)",
                  description:
                    "Second in command, responsible for cargo operations, stability, and deck department management.",
                  requirement1: "Chief Mate License",
                  requirement2: "Minimum 3 years as Officer",
                  requirement3: "Cargo handling experience",
                  requirement4: "Leadership skills",
                },
                secondOfficer: {
                  rank: "Second Officer",
                  salary: "$3,000 - $5,000",
                  title: "Second Officer",
                  description:
                    "Responsible for navigation, chart correction, and communication equipment maintenance.",
                  requirement1: "Officer of the Watch License",
                  requirement2: "Navigation experience",
                  requirement3: "GMDSS certificate",
                  requirement4: "Medical fitness",
                },
                thirdOfficer: {
                  rank: "Third Officer",
                  salary: "$2,500 - $4,000",
                  title: "Third Officer",
                  description:
                    "Assists in navigation watch, safety equipment maintenance, and life-saving appliances.",
                  requirement1: "Officer of the Watch License",
                  requirement2: "STCW basic training",
                  requirement3: "Firefighting certificate",
                  requirement4: "Entry-level position",
                },
                ratings: {
                  rank: "Deck Ratings",
                  salary: "$1,200 - $2,500",
                  title: "Deck Crew (AB/OS)",
                  description:
                    "Able Seamen and Ordinary Seamen responsible for deck maintenance, mooring operations, and watch duties.",
                  requirement1: "AB or OS certificate",
                  requirement2: "STCW basic safety training",
                  requirement3: "Sea service experience",
                  requirement4: "Physical fitness",
                },
              },
              skills: {
                navigation: "Navigation & Chart Work",
                cargo: "Cargo Operations",
                safety: "Safety Management",
                maintenance: "Deck Maintenance",
                communication: "GMDSS Communication",
                mooring: "Mooring Operations",
              },
            },
            engine: {
              subtitle: "Propulsion & Machinery",
              title: "Engine <span class='highlight'>Department</span>",
              description:
                "Skilled engineers and technicians responsible for propulsion systems, auxiliary machinery, and electrical systems.",
              positions: {
                chiefEngineer: {
                  rank: "Chief Engineer",
                  salary: "$6,000 - $12,000",
                  title: "Chief Engineer",
                  description:
                    "Overall responsibility for engine department operations, maintenance, and technical management.",
                  requirement1: "Chief Engineer License (Unlimited)",
                  requirement2: "Minimum 5 years as Second Engineer",
                  requirement3: "Management experience",
                  requirement4: "Budgeting skills",
                },
                secondEngineer: {
                  rank: "Second Engineer",
                  salary: "$4,500 - $8,000",
                  title: "Second Engineer",
                  description:
                    "Responsible for main engine, maintenance planning, and technical supervision.",
                  requirement1: "Second Engineer License",
                  requirement2: "Minimum 3 years as Engineer",
                  requirement3: "Maintenance planning experience",
                  requirement4: "Technical supervision skills",
                },
                thirdEngineer: {
                  rank: "Third Engineer",
                  salary: "$3,000 - $5,000",
                  title: "Third Engineer",
                  description:
                    "Responsible for auxiliary machinery, boilers, and electrical systems maintenance.",
                  requirement1: "Officer of the Watch (Engine)",
                  requirement2: "Auxiliary systems experience",
                  requirement3: "Electrical knowledge",
                  requirement4: "Troubleshooting skills",
                },
                fourthEngineer: {
                  rank: "Fourth Engineer",
                  salary: "$2,500 - $4,000",
                  title: "Fourth Engineer",
                  description:
                    "Entry-level engineering officer assisting in watchkeeping and maintenance duties.",
                  requirement1: "Officer of the Watch (Engine)",
                  requirement2: "Fresh graduate acceptable",
                  requirement3: "Basic engineering knowledge",
                  requirement4: "Willingness to learn",
                },
                ratings: {
                  rank: "Engine Ratings",
                  salary: "$1,200 - $2,500",
                  title: "Engine Room Crew",
                  description:
                    "Motormen, oilers, and wipers responsible for engine room operations and maintenance support.",
                  requirement1: "Engine Room Rating certificate",
                  requirement2: "STCW basic safety training",
                  requirement3: "Mechanical aptitude",
                  requirement4: "Physical fitness",
                },
              },
              skills: {
                mainEngine: "Main Engine Operation",
                electrical: "Electrical Systems",
                maintenance: "Preventive Maintenance",
                lubrication: "Lubrication Systems",
                boilers: "Boiler Operation",
                pneumatic: "Pneumatic Systems",
              },
            },
            hotel: {
              subtitle: "Hospitality & Services",
              title: "Hotel <span class='highlight'>Department</span>",
              description:
                "Hospitality professionals providing exceptional guest services, culinary experiences, and accommodation management.",
              positions: {
                hotelManager: {
                  rank: "Hotel Manager",
                  salary: "$4,000 - $8,000",
                  title: "Hotel Manager",
                  description:
                    "Overall responsibility for hotel operations, guest services, and department management.",
                  requirement1: "Hospitality management degree",
                  requirement2: "Minimum 5 years hotel experience",
                  requirement3: "Leadership and management skills",
                  requirement4: "Customer service excellence",
                },
                executiveChef: {
                  rank: "Executive Chef",
                  salary: "$3,500 - $6,500",
                  title: "Executive Chef",
                  description:
                    "Head of culinary department, menu planning, food quality control, and kitchen management.",
                  requirement1: "Culinary arts degree",
                  requirement2: "Minimum 8 years kitchen experience",
                  requirement3: "Menu planning expertise",
                  requirement4: "Food safety certification",
                },
                restaurantManager: {
                  rank: "Restaurant Manager",
                  salary: "$2,500 - $4,500",
                  title: "Restaurant Manager",
                  description:
                    "Manages restaurant operations, service quality, staff training, and guest satisfaction.",
                  requirement1: "Hospitality or related degree",
                  requirement2: "Minimum 3 years F&B experience",
                  requirement3: "Service training skills",
                  requirement4: "Multilingual preferred",
                },
                housekeepingSupervisor: {
                  rank: "Housekeeping Supervisor",
                  salary: "$1,800 - $3,000",
                  title: "Housekeeping Supervisor",
                  description:
                    "Oversees cabin cleaning, laundry operations, and accommodation maintenance.",
                  requirement1: "Housekeeping experience",
                  requirement2: "Supervisory skills",
                  requirement3: "Inventory management",
                  requirement4: "Attention to detail",
                },
                stewards: {
                  rank: "Stewards",
                  salary: "$1,200 - $2,200",
                  title: "Stewards / Wait Staff",
                  description:
                    "Provides food and beverage service, cabin service, and public area cleaning.",
                  requirement1: "High school diploma",
                  requirement2: "Service industry experience",
                  requirement3: "Good communication skills",
                  requirement4: "Customer service oriented",
                },
              },
              skills: {
                fnb: "Food & Beverage Service",
                housekeeping: "Housekeeping Excellence",
                guest: "Guest Relations",
                butler: "Butler Service",
                culinary: "Culinary Arts",
                language: "Multilingual Skills",
              },
            },
            entertainment: {
              subtitle: "Guest Activities & Entertainment",
              title:
                "Entertainment <span class='highlight'>Department</span>",
              description:
                "Talented performers, activity coordinators, and entertainment professionals creating memorable guest experiences.",
              positions: {
                cruiseDirector: {
                  rank: "Cruise Director",
                  salary: "$4,000 - $7,000",
                  title: "Cruise Director",
                  description:
                    "Oversees all entertainment programs, activities, and guest engagement initiatives.",
                  requirement1: "Entertainment management degree",
                  requirement2: "Minimum 5 years experience",
                  requirement3: "Public speaking skills",
                  requirement4: "Event planning expertise",
                },
                entertainers: {
                  rank: "Entertainers",
                  salary: "$2,000 - $5,000",
                  title: "Performers & Entertainers",
                  description:
                    "Singers, dancers, musicians, and variety artists providing stage performances and entertainment.",
                  requirement1: "Performance background",
                  requirement2: "Audition required",
                  requirement3: "Stage presence",
                  requirement4: "Versatility in performance",
                },
                activityCoordinator: {
                  rank: "Activity Coordinator",
                  salary: "$1,800 - $3,000",
                  title: "Activity Coordinator",
                  description:
                    "Plans and hosts guest activities, games, workshops, and social events.",
                  requirement1: "Recreation or related degree",
                  requirement2: "Event planning experience",
                  requirement3: "Outgoing personality",
                  requirement4: "Multilingual preferred",
                },
                youthStaff: {
                  rank: "Youth Staff",
                  salary: "$1,500 - $2,500",
                  title: "Youth Program Staff",
                  description:
                    "Provides childcare, youth activities, and educational programs for younger guests.",
                  requirement1: "Education or childcare background",
                  requirement2: "Child safety certification",
                  requirement3: "Creative activity planning",
                  requirement4: "Patience and enthusiasm",
                },
              },
              skills: {
                performing: "Stage Performance",
                games: "Game Hosting",
                engagement: "Guest Engagement",
                music: "Musical Talent",
                creative: "Creative Arts",
                language: "Multilingual MC",
              },
            },
            skillsTitle: "Key Skills & Competencies",
            feature1: {
              title: "Crew Recruitment & Selection",
              description:
                "Comprehensive recruitment process including screening, interviews, background checks, and selection of qualified seafarers.",
              item1: "Candidate sourcing and screening",
              item2: "Technical and psychometric testing",
              item3: "Medical examinations and fitness tests",
              item4: "Document verification and certification",
            },
            feature2: {
              title: "Training & Certification",
              description:
                "Specialized maritime training programs and certification management to meet international standards.",
              item1: "STCW training and certification",
              item2: "Safety and emergency procedures training",
              item3: "Specialized vessel type training",
              item4: "Continuous professional development",
            },
            feature3: {
              title: "Travel & Logistics Management",
              description:
                "Complete travel arrangements and logistics support for crew deployment and repatriation.",
              item1: "Flight and accommodation arrangements",
              item2: "Visa and immigration assistance",
              item3: "Airport transfers and ground transportation",
              item4: "Emergency travel coordination",
            },
            feature4: {
              title: "Crew Welfare & Support",
              description:
                "24/7 support services for crew members, ensuring their well-being and addressing concerns promptly.",
              item1: "24/7 emergency support",
              item2: "Contract management and administration",
              item3: "Family communication assistance",
              item4: "Medical and insurance coordination",
            },
            processTitle: "Our Crewing Process",
            step1: {
              title: "Requirement Analysis",
              description: "Understanding client needs and vessel requirements",
            },
            step2: {
              title: "Candidate Selection",
              description: "Screening and selecting qualified candidates",
            },
            step3: {
              title: "Documentation & Training",
              description: "Certification and pre-deployment training",
            },
            step4: {
              title: "Deployment & Support",
              description: "Travel arrangements and ongoing support",
            },
            recruitment: {
              subtitle: "Join Our Team",
              title: "Recruitment <span class='highlight'>Process</span>",
              description:
                "Our structured recruitment process ensures we select the best candidates for each position.",
              step1: {
                title: "Application Submission",
                description:
                  "Submit your application through our online portal with required documents.",
                detail1: "Complete online application form",
                detail2: "Upload CV/resume",
                detail3: "Submit copies of certificates",
                detail4: "Provide passport-sized photo",
              },
              step2: {
                title: "Document Screening",
                description:
                  "Our recruitment team reviews all applications and documents.",
                detail1: "Certificate verification",
                detail2: "Experience validation",
                detail3: "Medical fitness check",
                detail4: "Background screening",
              },
              step3: {
                title: "Interview & Assessment",
                description:
                  "Selected candidates undergo interviews and skill assessments.",
                detail1: "Technical interview",
                detail2: "HR interview",
                detail3: "Practical assessment",
                detail4: "Psychological evaluation",
              },
              step4: {
                title: "Training & Certification",
                description:
                  "Required training and certification completion.",
                detail1: "STCW basic safety training",
                detail2: "Company-specific training",
                detail3: "Security awareness training",
                detail4: "Medical examination",
              },
              step5: {
                title: "Deployment",
                description:
                  "Final placement and travel arrangements to join the vessel.",
                detail1: "Contract signing",
                detail2: "Travel arrangements",
                detail3: "Visa processing",
                detail4: "Joining instructions",
              },
              cta: {
                title: "Ready to Start Your Maritime Career?",
                description:
                  "Apply now and join our team of professional seafarers. We offer competitive packages and career growth opportunities.",
              },
            },
            documents: {
              subtitle: "Prepare Your Application",
              title: "Required <span class='highlight'>Documents</span>",
              category1: {
                title: "Personal Documents",
                item1: "Passport (minimum 2 years validity)",
                item2: "Seafarer's Identity Document (SID)",
                item3: "Birth certificate",
                item4: "Marriage certificate (if applicable)",
                item5: "Police clearance certificate",
              },
              category2: {
                title: "Professional Documents",
                item1: "Certificate of Competency (COC)",
                item2: "STCW certificates",
                item3: "Training certificates",
                item4: "Sea service records",
                item5: "Recommendation letters",
              },
              category3: {
                title: "Medical & Health",
                item1: "Valid medical fitness certificate",
                item2: "Vaccination records",
                item3: "Dental certificate",
                item4: "Eye examination report",
                item5: "COVID-19 vaccination certificate",
              },
            },
          },
          chandler: {
            subtitle: "Quality Marine Supplies",
            title: "Ship Chandler <span class='highlight'>Services</span>",
            description:
              "Comprehensive ship supply solutions providing quality provisions, stores, and equipment for vessels operating worldwide.",
            supplyTitle: "Complete Vessel Provisioning",
            supplyDescription:
              "We provide complete ship supply services including food provisions, technical stores, deck and engine stores, and bonded items.",
            category1: {
              title: "Food & Provisions",
              item1: "Fresh fruits and vegetables",
              item2: "Frozen meat and seafood",
              item3: "Dry stores and groceries",
              item4: "Beverages and dairy products",
            },
            category2: {
              title: "Technical Stores",
              item1: "Engine spare parts",
              item2: "Electrical components",
              item3: "Welding supplies",
              item4: "Tools and equipment",
            },
            category3: {
              title: "Deck & Bonded Items",
              item1: "Deck stores and equipment",
              item2: "Safety equipment",
              item3: "Bonded stores",
              item4: "Chemicals and lubricants",
            },
            captionTitle: "Quality Assured Supplies",
            captionText:
              "All supplies meet international quality standards and are delivered on time.",
            highlight1: {
              title: "24/7 Service Availability",
              description:
                "Round-the-clock service to meet urgent vessel requirements",
            },
            highlight2: {
              title: "Quality Certified Supplies",
              description:
                "All supplies meet international quality and safety standards",
            },
            highlight3: {
              title: "Global Network Coverage",
              description: "Supply capabilities at major ports worldwide",
            },
          },
          documentation: {
            subtitle: "Regulatory Compliance",
            title: "Vessel Documentation <span class='highlight'>Services</span>",
            description:
              "Comprehensive documentation support ensuring vessels comply with international maritime regulations and flag state requirements.",
            service1: {
              title: "Vessel Registration & Licensing",
              item1: "Initial vessel registration",
              item2: "Flag state documentation",
              item3: "Port state control compliance",
              item4: "Licensing and permits",
            },
            service2: {
              title: "Certificate Management",
              item1: "SOLAS certificates",
              item2: "MARPOL documentation",
              item3: "ISM/ISPS certificates",
              item4: "Class society certificates",
            },
            service3: {
              title: "Crew Documentation",
              item1: "Seafarer employment agreements",
              item2: "Certificate of competency",
              item3: "Medical certificates",
              item4: "Training record management",
            },
            complianceTitle: "Compliance Management Timeline",
            timeline1: {
              date: "Initial",
              title: "Document Audit",
              description: "Comprehensive review of existing documentation",
            },
            timeline2: {
              date: "2 Weeks",
              title: "Gap Analysis",
              description: "Identification of compliance requirements",
            },
            timeline3: {
              date: "4 Weeks",
              title: "Document Preparation",
              description: "Preparation of required certificates and documents",
            },
            timeline4: {
              date: "Ongoing",
              title: "Renewal Management",
              description:
                "Continuous monitoring and renewal of expiring documents",
            },
          },
          logistics: {
            subtitle: "Supply Chain Management",
            title: "Maritime Logistics <span class='highlight'>Services</span>",
            description:
              "End-to-end logistics solutions for maritime operations, ensuring timely delivery of supplies and equipment to vessels worldwide.",
            service1: {
              title: "Port Logistics",
              description:
                "Coordination of port operations, cargo handling, and storage solutions",
              item1: "Port agency services",
              item2: "Cargo handling coordination",
              item3: "Customs clearance assistance",
              item4: "Warehousing and storage",
            },
            service2: {
              title: "Supply Chain Management",
              description:
                "Complete supply chain solutions from sourcing to delivery",
              item1: "Supplier sourcing and management",
              item2: "Inventory management",
              item3: "Order processing and tracking",
              item4: "Just-in-time delivery coordination",
            },
            service3: {
              title: "Emergency Logistics",
              description:
                "Rapid response logistics for urgent vessel requirements",
              item1: "Emergency spare parts delivery",
              item2: "Medical supplies transportation",
              item3: "Technical support equipment delivery",
              item4: "Crisis management logistics",
            },
          },
          technical: {
            subtitle: "Technical Support",
            title: "Technical & Engineering <span class='highlight'>Services</span>",
            description:
              "Comprehensive technical support and engineering services for vessel maintenance, repair, and operational efficiency.",
            service1: {
              title: "Vessel Maintenance Support",
              description:
                "Preventive and corrective maintenance planning and execution",
              feature1: "Maintenance planning",
              feature2: "Spare parts management",
              feature3: "Technical supervision",
              feature4: "Quality control",
            },
            service2: {
              title: "Technical Consulting",
              description:
                "Expert technical advice and solutions for maritime operations",
              feature1: "Operational efficiency",
              feature2: "Fuel optimization",
              feature3: "Equipment selection",
              feature4: "Compliance consulting",
            },
          },
        },
        crewing: {
          applyNow: "Apply Now",
          stats: {
            crew: "Certified Seafarers",
            vessels: "Vessels Served",
            years: "Years Experience",
          },
          nav: {
            deck: "Deck Department",
            engine: "Engine Department",
            hotel: "Hotel Department",
            entertainment: "Entertainment Department",
            recruitment: "Recruitment Process",
          },
          requirements: "Requirements:",
          skillsTitle: "Key Skills & Competencies",
          certificates: {
            master: "Master License",
            stcw: "STCW",
            gmdss: "GMDSS",
            chiefMate: "Chief Mate License",
            bulk: "Bulk Cargo",
            oow: "OOW License",
            ecdis: "ECDIS",
            ff: "Firefighting",
            ab: "AB Certificate",
            pscrb: "PSCRB",
            chiefEng: "Chief Engineer License",
            management: "Management Level",
            secondEng: "Second Engineer License",
            maintenance: "Maintenance",
            oowEng: "OOW (Engine) License",
            electrical: "Electrical",
            basic: "Basic Training",
            erRating: "ER Rating Certificate",
            welding: "Welding",
            hospitality: "Hospitality Management",
            foodSafety: "Food Safety",
            leadership: "Leadership",
            culinary: "Culinary Arts",
            haccp: "HACCP",
          },
          deck: {
            subtitle: "Navigation & Operations",
            title: "Deck <span class='highlight'>Department</span>",
            description: "Professional deck officers and crew responsible for navigation, cargo operations, and vessel safety.",
            positions: {
              master: {
                rank: "Master / Captain",
                salary: "$5,000 - $12,000",
                title: "Master (Captain)",
                description: "Overall command of the vessel, responsible for safety, navigation, and compliance with international regulations.",
                requirement1: "Master Mariner License (Unlimited)",
                requirement2: "Minimum 5 years as Chief Officer",
                requirement3: "STCW certifications",
                requirement4: "Valid medical certificate"
              },
              chiefOfficer: {
                rank: "Chief Officer",
                salary: "$4,000 - $8,000",
                title: "Chief Officer (First Mate)",
                description: "Second in command, responsible for cargo operations, stability, and deck department management.",
                requirement1: "Chief Mate License",
                requirement2: "Minimum 3 years as Officer",
                requirement3: "Cargo handling experience",
                requirement4: "Leadership skills"
              },
              secondOfficer: {
                rank: "Second Officer",
                salary: "$3,000 - $5,000",
                title: "Second Officer",
                description: "Responsible for navigation, chart correction, and communication equipment maintenance.",
                requirement1: "Officer of the Watch License",
                requirement2: "Navigation experience",
                requirement3: "GMDSS certificate",
                requirement4: "Medical fitness"
              },
              thirdOfficer: {
                rank: "Third Officer",
                salary: "$2,500 - $4,000",
                title: "Third Officer",
                description: "Assists in navigation watch, safety equipment maintenance, and life-saving appliances.",
                requirement1: "Officer of the Watch License",
                requirement2: "STCW basic training",
                requirement3: "Firefighting certificate",
                requirement4: "Entry-level position"
              },
              ratings: {
                rank: "Deck Ratings",
                salary: "$1,200 - $2,500",
                title: "Deck Crew (AB/OS)",
                description: "Able Seamen and Ordinary Seamen responsible for deck maintenance, mooring operations, and watch duties.",
                requirement1: "AB or OS certificate",
                requirement2: "STCW basic safety training",
                requirement3: "Sea service experience",
                requirement4: "Physical fitness"
              }
            },
            skills: {
              navigation: "Navigation & Chart Work",
              cargo: "Cargo Operations",
              safety: "Safety Management",
              maintenance: "Deck Maintenance",
              communication: "GMDSS Communication",
              mooring: "Mooring Operations"
            }
          },
          engine: {
            subtitle: "Propulsion & Machinery",
            title: "Engine <span class='highlight'>Department</span>",
            description: "Skilled engineers and technicians responsible for propulsion systems, auxiliary machinery, and electrical systems.",
            positions: {
              chiefEngineer: {
                rank: "Chief Engineer",
                salary: "$6,000 - $12,000",
                title: "Chief Engineer",
                description: "Overall responsibility for engine department operations, maintenance, and technical management.",
                requirement1: "Chief Engineer License (Unlimited)",
                requirement2: "Minimum 5 years as Second Engineer",
                requirement3: "Management experience",
                requirement4: "Budgeting skills"
              },
              secondEngineer: {
                rank: "Second Engineer",
                salary: "$4,500 - $8,000",
                title: "Second Engineer",
                description: "Responsible for main engine, maintenance planning, and technical supervision.",
                requirement1: "Second Engineer License",
                requirement2: "Minimum 3 years as Engineer",
                requirement3: "Maintenance planning experience",
                requirement4: "Technical supervision skills"
              },
              thirdEngineer: {
                rank: "Third Engineer",
                salary: "$3,000 - $5,000",
                title: "Third Engineer",
                description: "Responsible for auxiliary machinery, boilers, and electrical systems maintenance.",
                requirement1: "Officer of the Watch (Engine)",
                requirement2: "Auxiliary systems experience",
                requirement3: "Electrical knowledge",
                requirement4: "Troubleshooting skills"
              },
              fourthEngineer: {
                rank: "Fourth Engineer",
                salary: "$2,500 - $4,000",
                title: "Fourth Engineer",
                description: "Entry-level engineering officer assisting in watchkeeping and maintenance duties.",
                requirement1: "Officer of the Watch (Engine)",
                requirement2: "Fresh graduate acceptable",
                requirement3: "Basic engineering knowledge",
                requirement4: "Willingness to learn"
              },
              ratings: {
                rank: "Engine Ratings",
                salary: "$1,200 - $2,500",
                title: "Engine Room Crew",
                description: "Motormen, oilers, and wipers responsible for engine room operations and maintenance support.",
                requirement1: "Engine Room Rating certificate",
                requirement2: "STCW basic safety training",
                requirement3: "Mechanical aptitude",
                requirement4: "Physical fitness"
              }
            },
            skills: {
              mainEngine: "Main Engine Operation",
              electrical: "Electrical Systems",
              maintenance: "Preventive Maintenance",
              lubrication: "Lubrication Systems",
              boilers: "Boiler Operation",
              pneumatic: "Pneumatic Systems"
            }
          },
          hotel: {
            subtitle: "Hospitality & Services",
            title: "Hotel <span class='highlight'>Department</span>",
            description: "Hospitality professionals providing exceptional guest services, culinary experiences, and accommodation management.",
            positions: {
              hotelManager: {
                rank: "Hotel Manager",
                salary: "$4,000 - $8,000",
                title: "Hotel Manager",
                description: "Overall responsibility for hotel operations, guest services, and department management.",
                requirement1: "Hospitality management degree",
                requirement2: "Minimum 5 years hotel experience",
                requirement3: "Leadership and management skills",
                requirement4: "Customer service excellence"
              },
              executiveChef: {
                rank: "Executive Chef",
                salary: "$3,500 - $6,500",
                title: "Executive Chef",
                description: "Head of culinary department, menu planning, food quality control, and kitchen management.",
                requirement1: "Culinary arts degree",
                requirement2: "Minimum 8 years kitchen experience",
                requirement3: "Menu planning expertise",
                requirement4: "Food safety certification"
              }
            }
          },
        },

        // About Preview
        about: {
          subtitle: "About Our Company",
          title: "Your Trusted <span class='highlight'>Maritime Partner</span>",
          description1:
            "PT Indo OceanCrew Services is an Indonesian maritime company providing qualified seafarers and comprehensive ship supply solutions for the global shipping industry.",
          description2:
            "As a trusted Crewing Agency and Ship Chandler, we offer end-to-end services — from crew recruitment and deployment to vessel provisioning and logistics.",
          feature1: "MLC, STCW, ISM Compliant",
          feature2: "Global Network",
          feature3: "24/7 Support",
          feature4: "Quality Assured",
          learnMoreBtn: "Learn More About Us",
          experience: "Years Experience",
        },

        // Core Services
        coreServices: {
          subtitle: "What We Offer",
          title:
            "Comprehensive <span class='highlight'>Maritime Solutions</span>",
          description:
            "With excellence in execution and innovative maritime solutions, we are committed to delivering outstanding value to our partners.",
          service1: {
            title: "Crewing Agency Services",
            description:
              "We provide professional crew management and recruitment solutions for a wide range of vessels. Our certified seafarers are trained to meet international standards.",
            feature1: "Crew Recruitment & Selection",
            feature2: "Training & Certification",
            feature3: "Travel & Logistics",
            feature4: "Crew Welfare Management",
          },
          service2: {
            title: "Shipowner Cooperation",
            description:
              "Strong collaboration between the vessel's crew and shipowners is essential to ensure the delivery of high-quality, standardized services.",
            feature1: "Strategic Partnership",
            feature2: "Performance Monitoring",
            feature3: "Compliance Assurance",
            feature4: "Continuous Improvement",
          },
          service3: {
            title: "Document Ship Services",
            description:
              "We provide comprehensive vessel documentation support to help ship owners and operators maintain compliance with local and international maritime regulations.",
            feature1: "Registration & Licensing",
            feature2: "Certification Management",
            feature3: "Compliance Documentation",
            feature4: "Audit Preparation",
          },
        },

        // Statistics
        stats: {
          crew: "Certified Seafarers",
          vessels: "Vessels Served",
          countries: "Countries Covered",
          support: "24/7 Support",
        },

        // CTA Section
        cta: {
          title: "Ready to Partner with Maritime Experts?",
          description:
            "Connect with us for reliable crew management, ship supply solutions, and comprehensive maritime services that meet global standards.",
          contactBtn: "Contact Us Today",
        },

        // Footer
        footer: {
          tagline: "Professional Maritime Solutions",
          description:
            "Leading maritime service provider specializing in crew management and ship supply solutions for the global shipping industry.",
          services: "Services",
          company: "Company",
          contact: "Contact",
          careers: "Careers",
          blog: "Blog",
          address: "Menara Cakrawala lt 15 no 1506 jl M.H. Thamrin Kec. Menteng, Kota Jakarta Pusat 10340",
          hours: "Mon - Fri: 8:00 - 17:00",
          rights: "All rights reserved.",
          privacy: "Privacy Policy",
          terms: "Terms of Service",
          cookies: "Cookies Policy",
        },

        // Services Page
        servicesPage: {
          title: "Our Services - PT Indo OceanCrew Services",
          heroTitle: "Our Professional Services",
          heroSubtitle:
            "Comprehensive maritime solutions for global shipping operations, ensuring safety, efficiency, and compliance.",
        },
        servicesNav: {
          crewing: "Crewing Agency",
          chandler: "Ship Chandler",
          documentation: "Vessel Documentation",
          logistics: "Maritime Logistics",
          technical: "Technical Services",
        },

        // About Page
        aboutPage: {
          title: "About Us - PT Indo OceanCrew Services",
          heroTitle: "About Our Company",
          heroSubtitle:
            "Professional maritime services provider with years of experience in crew management and ship chandler services.",
          overviewSubtitle: "Company Overview",
          overviewTitle:
            "Your Trusted <span class='highlight'>Maritime Partner</span>",
          leadText:
            "PT Indo OceanCrew Services is a trusted maritime service provider specializing in crew management and ship supply solutions. With an extensive network of certified seafarers and quality marine provisions, we ensure every vessel under our care operates smoothly, safely, and efficiently.",
          missionTitle: "Our Mission",
          missionText:
            "To deliver excellence, reliability, and global standards to meet the dynamic needs of the shipping industry through professional crew management and comprehensive ship supply solutions.",
          visionTitle: "Our Vision",
          visionText:
            "To be the leading maritime service provider in Southeast Asia, recognized for our integrity, professionalism, and commitment to sustainable maritime operations.",
          experienceText: "Years",
          valuesSubtitle: "Our Principles",
          valuesTitle: "Core <span class='highlight'>Values</span>",
          value1Title: "Integrity",
          value1Desc:
            "We conduct business with honesty, transparency, and ethical standards in all our operations.",
          value2Title: "Excellence",
          value2Desc:
            "We strive for the highest quality in every service we provide, continuously improving our standards.",
          value3Title: "Collaboration",
          value3Desc:
            "We build strong partnerships with clients, crew, and stakeholders for mutual success.",
          value4Title: "Safety",
          value4Desc:
            "We prioritize the safety and well-being of our crew and the vessels we serve above all else.",
          certSubtitle: "Quality Assurance",
          certTitle:
            "Certifications & <span class='highlight'>Compliance</span>",
          cert1Title: "MLC 2006 Compliant",
          cert1Desc:
            "Maritime Labour Convention standards for crew welfare and working conditions.",
          cert2Title: "STCW Certified",
          cert2Desc:
            "International convention on standards of training, certification and watchkeeping.",
          cert3Title: "ISM Code",
          cert3Desc:
            "International Safety Management Code for safe management and operation of ships.",
          teamSubtitle: "Meet Our Team",
          teamTitle: "Leadership & <span class='highlight'>Expertise</span>",
          team1Name: "John Maritime",
          team1Position: "Chief Executive Officer",
          team1Bio:
            "25+ years of maritime industry experience with expertise in crew management and vessel operations.",
          team2Name: "Sarah Ocean",
          team2Position: "Operations Director",
          team2Bio:
            "Specialized in maritime logistics and crew deployment with 15 years of industry experience.",
          team3Name: "Michael Seafarer",
          team3Position: "Compliance Manager",
          team3Bio:
            "Expert in international maritime regulations, certifications, and quality assurance systems.",
        },

        // Contact Page
        contactPage: {
          title: "Contact Us - PT Indo OceanCrew Services",
          heroTitle: "Contact Us",
          heroSubtitle:
            "Ready to partner with maritime experts? Get in touch with our team for reliable crew management and ship supply solutions.",
        },
        contact: {
          getQuote: "Get Quote",
          support: "Support Available",
          response: "Response Time",
          satisfaction: "Client Satisfaction",
          info: {
            subtitle: "Get In Touch",
            title: "Contact Information",
            description:
              "Reach out to us through multiple channels. Our team is available around the clock to support your maritime needs.",
          },
          office: {
            title: "Head Office",
            address: "Menara Cakrawala lt 15 no 1506 jl M.H. Thamrin\nKec. Menteng, Kota Jakarta Pusat 10340",
          },
          getDirections: "Get Directions",
          phone: {
            title: "Phone & WhatsApp",
          },
          "24hours": "24/7 Emergency Support",
          email: {
            title: "Email",
          },
          responseTime: "< 2 hours response",
          hours: {
            title: "Business Hours",
            monday: "Monday - Friday",
            saturday: "Saturday",
            emergency: "Emergency",
            24: "24/7",
          },
          form: {
            subtitle: "Send Message",
            title: "Get Your Free Quote",
            description:
              "Fill out the form below and our team will get back to you within 2 hours with a customized solution for your maritime needs.",
            firstName: "First Name *",
            lastName: "Last Name *",
            email: "Email Address *",
            phone: "Phone Number",
            company: "Company Name",
            service: "Service Interested In *",
            selectService: "Select a service",
            other: "Other",
            subject: "Subject *",
            message: "Message *",
            messagePlaceholder:
              "Please describe your requirements in detail...",
            newsletter:
              "Subscribe to our newsletter for maritime industry updates",
            submit: "Send Message",
          },
          quick: {
            title: "Quick Contact",
            description:
              "Need immediate assistance? Call us directly or send a WhatsApp message.",
            call: "Call Now",
            whatsapp: "WhatsApp",
          },
          services: {
            title: "Our Services",
          },
        },

        // Crewing Page
        crewingPage: {
          title: "Crewing Services - PT Indo OceanCrew Services",
          heroTitle: "Professional Crewing Services",
          heroSubtitle:
            "Qualified seafarers for all vessel departments, ensuring safe and efficient maritime operations worldwide.",
        },
        crewing: {
          applyNow: "Apply Now",
          stats: {
            crew: "Certified Seafarers",
            vessels: "Vessels Served",
            years: "Years Experience",
          },
          nav: {
            deck: "Deck Department",
            engine: "Engine Department",
            hotel: "Hotel Department",
            entertainment: "Entertainment Department",
            recruitment: "Recruitment Process",
          },
        },

        // Contact Form Section
        contact: {
          form: {
            subtitle: "Send Message",
            title: "Contact <span class='highlight'>Form</span>",
            description: "Fill out the form below completely and our team will contact you within 2 hours.",
            step1: "Basic Info",
            step2: "Service Details",
            step3: "Confirmation",
            step1Title: "Your Contact Information",
            step1Desc: "Please complete your contact information",
            step2Title: "Your Service Needs",
            step2Desc: "Select the services you need",
            step3Title: "Message Details & Attachments",
            step3Desc: "Add message and attachments if needed",
            fullName: "Full Name",
            email: "Email",
            phone: "Phone Number/WhatsApp",
            company: "Company Name",
            position: "Position",
            companyType: "Company Type",
            serviceType: "Service Type Required",
            vesselType: "Vessel Type",
            urgency: "Urgency Level",
            crewSize: "Number of Crew Required",
            budgetRange: "Budget Range",
            subject: "Message Subject",
            message: "Request Details",
            attachment: "File Attachment (Optional)",
            referral: "How did you hear about us?",
            prev: "Previous",
            next: "Next",
            submit: "Submit Request",
            successTitle: "Request Submitted!",
            successMsg: "Thank you for your request. Our team will contact you within 2 hours via the email and phone you provided.",
            requestId: "Request ID:",
            newRequest: "Submit New Request",
          },
        },
      },

      id: {
        // Meta & Page Titles
        title:
          "PT Indo OceanCrew Services - Jasa Crewing Profesional & Ship Chandler",
        tagline: "Solusi Maritim Profesional",

        // Navigation
        nav: {
          home: "Beranda",
          about: "Tentang Kami",
          services: "Layanan",
          departments: "Crewing",
          contact: "Kontak",
          contactBtn: "Hubungi Kami",
          crewing: "Layanan Crewing",
          chandler: "Ship Chandler",
          documentation: "Dokumentasi Kapal",
          logistics: "Logistik Maritim",
          crew: "Crew Kami",
        },

        // Hero Section
        hero: {
          subtitle: "Jasa Crewing Agency & Ship Chandler Profesional",
          title:
            "Keunggulan Maritim <span class='highlight'>Melintasi Samudra</span>",
          description:
            "Penyedia jasa maritim terpercaya yang mengkhususkan diri dalam manajemen kru dan solusi pasokan kapal. Memastikan setiap kapal beroperasi dengan lancar, aman, dan efisien.",
          servicesBtn: "Layanan Kami",
          contactBtn: "Dapatkan Penawaran",
          subtitle2: "Solusi Maritim Global",
          description2:
            "Layanan maritim komprehensif yang memenuhi standar internasional (MLC, STCW, ISM Code) dengan integritas, keandalan, dan keunggulan.",
        },

        // Services Highlights
        services: {
          highlight1: {
            title: "Layanan Crewing Agency",
            desc: "Solusi manajemen dan rekrutmen kru profesional untuk semua jenis kapal",
          },
          highlight2: {
            title: "Layanan Ship Chandler",
            desc: "Provisioning laut berkualitas dan solusi pasokan untuk armada global",
          },
          highlight3: {
            title: "Dokumentasi Kapal",
            desc: "Dukungan kepatuhan dengan regulasi maritim internasional",
          },
          learnMore: "Pelajari Lebih Lanjut",
        },

        // About Preview
        about: {
          subtitle: "Tentang Perusahaan Kami",
          title: "Mitra Maritim <span class='highlight'>Terpercaya Anda</span>",
          description1:
            "PT Indo OceanCrew Services adalah perusahaan maritim Indonesia yang menyediakan pelaut berkualitas dan solusi pasokan kapal komprehensif untuk industri pelayaran global.",
          description2:
            "Sebagai Crewing Agency dan Ship Chandler terpercaya, kami menawarkan layanan end-to-end — dari rekrutmen dan penempatan kru hingga provisioning kapal dan logistik.",
          feature1: "Patuh MLC, STCW, ISM",
          feature2: "Jaringan Global",
          feature3: "Dukungan 24/7",
          feature4: "Kualitas Terjamin",
          learnMoreBtn: "Pelajari Tentang Kami",
          experience: "Tahun Pengalaman",
        },

        // Core Services
        coreServices: {
          subtitle: "Apa yang Kami Tawarkan",
          title: "Solusi Maritim <span class='highlight'>Komprehensif</span>",
          description:
            "Dengan keunggulan dalam eksekusi dan solusi maritim inovatif, kami berkomitmen untuk memberikan nilai luar biasa kepada mitra kami.",
          service1: {
            title: "Layanan Crewing Agency",
            description:
              "Kami menyediakan solusi manajemen dan rekrutmen kru profesional untuk berbagai jenis kapal. Pelaut bersertifikat kami dilatih untuk memenuhi standar internasional.",
            feature1: "Rekrutmen & Seleksi Kru",
            feature2: "Pelatihan & Sertifikasi",
            feature3: "Perjalanan & Logistik",
            feature4: "Manajemen Kesejahteraan Kru",
          },
          service2: {
            title: "Kerjasama Pemilik Kapal",
            description:
              "Kolaborasi yang kuat antara kru kapal dan pemilik kapal sangat penting untuk memastikan penyampaian layanan berkualitas tinggi dan terstandarisasi.",
            feature1: "Kemitraan Strategis",
            feature2: "Pemantauan Kinerja",
            feature3: "Jaminan Kepatuhan",
            feature4: "Perbaikan Berkelanjutan",
          },
          service3: {
            title: "Layanan Dokumentasi Kapal",
            description:
              "Kami menyediakan dukungan dokumentasi kapal yang komprehensif untuk membantu pemilik dan operator kapal mempertahankan kepatuhan dengan regulasi maritim lokal dan internasional.",
            feature1: "Pendaftaran & Perizinan",
            feature2: "Manajemen Sertifikasi",
            feature3: "Dokumentasi Kepatuhan",
            feature4: "Persiapan Audit",
          },
        },

        // Statistics
        stats: {
          crew: "Pelaut Bersertifikat",
          vessels: "Kapal yang Dilayani",
          countries: "Negara Tercakup",
          support: "24/7 Dukungan",
        },

        // CTA Section
        cta: {
          title: "Siap Bermitra dengan Ahli Maritim?",
          description:
            "Hubungi kami untuk manajemen kru yang andal, solusi pasokan kapal, dan layanan maritim komprehensif yang memenuhi standar global.",
          contactBtn: "Hubungi Kami Sekarang",
        },

        // Footer
        footer: {
          tagline: "Solusi Maritim Profesional",
          description:
            "Penyedia jasa maritim terkemuka yang mengkhususkan diri dalam manajemen kru dan solusi pasokan kapal untuk industri pelayaran global.",
          services: "Layanan",
          company: "Perusahaan",
          contact: "Kontak",
          careers: "Karir",
          blog: "Blog",
          address: "Menara Cakrawala lt 15 no 1506 jl M.H. Thamrin Kec. Menteng, Kota Jakarta Pusat 10340",
          hours: "Sen - Jum: 8:00 - 17:00",
          rights: "Hak cipta dilindungi.",
          privacy: "Kebijakan Privasi",
          terms: "Syarat Layanan",
          cookies: "Kebijakan Cookies",
        },

        // Services Page
        servicesPage: {
          title: "Layanan Kami - PT Indo OceanCrew Services",
          heroTitle: "Layanan Profesional Kami",
          heroSubtitle:
            "Solusi maritim komprehensif untuk operasi pelayaran global, memastikan keselamatan, efisiensi, dan kepatuhan.",
        },
        servicesNav: {
          crewing: "Layanan Crewing",
          chandler: "Ship Chandler",
          documentation: "Dokumentasi Kapal",
          logistics: "Logistik Maritim",
          technical: "Layanan Teknis",
        },

        // About Page
        aboutPage: {
          title: "Tentang Kami - PT Indo OceanCrew Services",
          heroTitle: "Tentang Perusahaan Kami",
          heroSubtitle:
            "Penyedia layanan maritim profesional dengan pengalaman bertahun-tahun dalam manajemen kru dan layanan ship chandler.",
          overviewSubtitle: "Gambaran Perusahaan",
          overviewTitle:
            "Mitra Maritim <span class='highlight'>Terpercaya Anda</span>",
          leadText:
            "PT Indo OceanCrew Services adalah penyedia layanan maritim terpercaya yang mengkhususkan diri dalam manajemen kru dan solusi pasokan kapal. Dengan jaringan luas pelaut bersertifikat dan provisioning laut berkualitas, kami memastikan setiap kapal yang kami layani beroperasi dengan lancar, aman, dan efisien.",
          missionTitle: "Misi Kami",
          missionText:
            "Memberikan keunggulan, keandalan, dan standar global untuk memenuhi kebutuhan dinamis industri pelayaran melalui manajemen kru profesional dan solusi pasokan kapal yang komprehensif.",
          visionTitle: "Visi Kami",
          visionText:
            "Menjadi penyedia layanan maritim terkemuka di Asia Tenggara, diakui atas integritas, profesionalisme, dan komitmen kami terhadap operasi maritim yang berkelanjutan.",
          experienceText: "Tahun",
          valuesSubtitle: "Prinsip Kami",
          valuesTitle: "Nilai Inti <span class='highlight'>Kami</span>",
          value1Title: "Integritas",
          value1Desc:
            "Kami menjalankan bisnis dengan kejujuran, transparansi, dan standar etika dalam semua operasi kami.",
          value2Title: "Keunggulan",
          value2Desc:
            "Kami berusaha untuk kualitas tertinggi dalam setiap layanan yang kami berikan, terus meningkatkan standar kami.",
          value3Title: "Kolaborasi",
          value3Desc:
            "Kami membangun kemitraan yang kuat dengan klien, kru, dan pemangku kepentingan untuk kesuksesan bersama.",
          value4Title: "Keselamatan",
          value4Desc:
            "Kami mengutamakan keselamatan dan kesejahteraan kru kami serta kapal yang kami layani di atas segalanya.",
          certSubtitle: "Jaminan Kualitas",
          certTitle: "Sertifikasi & <span class='highlight'>Kepatuhan</span>",
          cert1Title: "Patuh MLC 2006",
          cert1Desc:
            "Standar Konvensi Perburuhan Maritim untuk kesejahteraan kru dan kondisi kerja.",
          cert2Title: "Bersertifikat STCW",
          cert2Desc:
            "Konvensi internasional tentang standar pelatihan, sertifikasi dan penjagaan.",
          cert3Title: "Kode ISM",
          cert3Desc:
            "Kode Manajemen Keselamatan Internasional untuk manajemen dan pengoperasian kapal yang aman.",
          teamSubtitle: "Tim Kami",
          teamTitle: "Kepemimpinan & <span class='highlight'>Keahlian</span>",
          team1Name: "John Maritime",
          team1Position: "Direktur Utama",
          team1Bio:
            "Pengalaman 25+ tahun di industri maritim dengan keahlian dalam manajemen kru dan operasi kapal.",
          team2Name: "Sarah Ocean",
          team2Position: "Direktur Operasi",
          team2Bio:
            "Spesialis dalam logistik maritim dan penempatan kru dengan 15 tahun pengalaman industri.",
          team3Name: "Michael Seafarer",
          team3Position: "Manajer Kepatuhan",
          team3Bio:
            "Ahli dalam regulasi maritim internasional, sertifikasi, dan sistem jaminan kualitas.",
        },

        // Contact Page
        contactPage: {
          title: "Hubungi Kami - PT Indo OceanCrew Services",
          heroTitle: "Hubungi Kami",
          heroSubtitle:
            "Siap bermitra dengan ahli maritim? Hubungi tim kami untuk manajemen kru yang andal dan solusi pasokan kapal.",
        },
        contact: {
          getQuote: "Dapatkan Penawaran",
          support: "Dukungan Tersedia",
          response: "Waktu Respons",
          satisfaction: "Kepuasan Klien",
          info: {
            subtitle: "Hubungi Kami",
            title: "Informasi Kontak",
            description:
              "Hubungi kami melalui berbagai saluran. Tim kami tersedia 24 jam untuk mendukung kebutuhan maritim Anda.",
          },
          office: {
            title: "Kantor Pusat",
            address: "Menara Cakrawala lt 15 no 1506 jl M.H. Thamrin\nKec. Menteng, Kota Jakarta Pusat 10340",
          },
          getDirections: "Dapatkan Petunjuk",
          phone: {
            title: "Telepon & WhatsApp",
          },
          "24hours": "Dukungan Darurat 24/7",
          email: {
            title: "Email",
          },
          responseTime: "< 2 jam respons",
          hours: {
            title: "Jam Kerja",
            monday: "Senin - Jumat",
            saturday: "Sabtu",
            emergency: "Darurat",
            24: "24/7",
          },
          form: {
            subtitle: "Kirim Pesan",
            title: "Dapatkan Penawaran Gratis Anda",
            description:
              "Isi formulir di bawah ini dan tim kami akan menghubungi Anda dalam waktu 2 jam dengan solusi yang disesuaikan untuk kebutuhan maritim Anda.",
            firstName: "Nama Depan *",
            lastName: "Nama Belakang *",
            email: "Alamat Email *",
            phone: "Nomor Telepon",
            company: "Nama Perusahaan",
            service: "Layanan yang Diminati *",
            selectService: "Pilih layanan",
            other: "Lainnya",
            subject: "Subjek *",
            message: "Pesan *",
            messagePlaceholder:
              "Silakan jelaskan persyaratan Anda secara detail...",
            newsletter:
              "Berlangganan newsletter kami untuk pembaruan industri maritim",
            submit: "Kirim Pesan",
          },
          quick: {
            title: "Kontak Cepat",
            description:
              "Butuh bantuan segera? Hubungi kami langsung atau kirim pesan WhatsApp.",
            call: "Telepon Sekarang",
            whatsapp: "WhatsApp",
          },
          services: {
            title: "Layanan Kami",
          },
        },

        // Crewing Page
        crewingPage: {
          title: "Layanan Crewing - PT Indo OceanCrew Services",
          heroTitle: "Layanan Crewing Profesional",
          heroSubtitle:
            "Pelaut berkualitas untuk semua departemen kapal, memastikan operasi maritim yang aman dan efisien di seluruh dunia.",
        },
        crewing: {
          applyNow: "Daftar Sekarang",
          stats: {
            crew: "Pelaut Bersertifikat",
            vessels: "Kapal yang Dilayani",
            years: "Tahun Pengalaman",
          },
          requirements: "Persyaratan:",
          certificates: {
            master: "Lisensi Master",
            stcw: "STCW",
            gmdss: "GMDSS",
            chiefMate: "Lisensi Chief Mate",
            bulk: "Muatan Curah",
            oow: "Lisensi OOW",
            ecdis: "ECDIS",
            ff: "Pemadam Kebakaran",
            ab: "Sertifikat AB",
            pscrb: "PSCRB",
            chiefEng: "Lisensi Chief Engineer",
            management: "Tingkat Manajemen",
            secondEng: "Lisensi Second Engineer",
            maintenance: "Pemeliharaan",
            oowEng: "Lisensi OOW (Engine)",
            electrical: "Elektrik",
            basic: "Pelatihan Dasar",
            erRating: "Sertifikat ER Rating",
            welding: "Pengelasan",
            hospitality: "Manajemen Hospitality",
            foodSafety: "Keamanan Pangan",
            leadership: "Kepemimpinan",
            culinary: "Seni Kuliner",
            haccp: "HACCP",
            fnb: "Manajemen F&B",
            wine: "Layanan Wine",
            service: "Layanan Unggul",
            housekeeping: "Housekeeping",
            cleaning: "Prosedur Pembersihan",
            supervisory: "Supervisi",
            entertainment: "Manajemen Hiburan",
            mc: "Master of Ceremonies",
            performing: "Seni Pertunjukan",
            music: "Musik/Tari",
            acting: "Akting",
            recreation: "Manajemen Rekreasi",
            event: "Perencanaan Acara",
            firstAid: "Pertolongan Pertama",
            childcare: "Perawatan Anak",
            education: "Pendidikan",
          },
          nav: {
            deck: "Departemen Deck",
            engine: "Departemen Mesin",
            hotel: "Departemen Hotel",
            entertainment: "Departemen Hiburan",
            recruitment: "Proses Rekrutmen",
          },
          deck: {
            subtitle: "Navigasi & Operasi",
            title: "Departemen <span class='highlight'>Deck</span>",
            description:
              "Petugas deck profesional dan kru yang bertanggung jawab atas navigasi, operasi kargo, dan keselamatan kapal.",
            positions: {
              master: {
                rank: "Master / Kapten",
                salary: "$5,000 - $12,000",
                title: "Master (Kapten)",
                description:
                  "Komando keseluruhan kapal, bertanggung jawab atas keselamatan, navigasi, dan kepatuhan dengan regulasi internasional.",
                requirement1: "Lisensi Master Mariner (Unlimited)",
                requirement2: "Minimal 5 tahun sebagai Chief Officer",
                requirement3: "Sertifikasi STCW",
                requirement4: "Sertifikat medis valid",
              },
              chiefOfficer: {
                rank: "Chief Officer",
                salary: "$4,000 - $8,000",
                title: "Chief Officer (First Mate)",
                description:
                  "Wakil komando, bertanggung jawab atas operasi kargo, stabilitas, dan manajemen departemen deck.",
                requirement1: "Lisensi Chief Mate",
                requirement2: "Minimal 3 tahun sebagai Officer",
                requirement3: "Pengalaman penanganan kargo",
                requirement4: "Keterampilan kepemimpinan",
              },
              secondOfficer: {
                rank: "Second Officer",
                salary: "$3,000 - $5,000",
                title: "Second Officer",
                description:
                  "Bertanggung jawab atas navigasi, koreksi chart, dan perawatan peralatan komunikasi.",
                requirement1: "Lisensi Officer of the Watch",
                requirement2: "Pengalaman navigasi",
                requirement3: "Sertifikat GMDSS",
                requirement4: "Kebugaran medis",
              },
              thirdOfficer: {
                rank: "Third Officer",
                salary: "$2,500 - $4,000",
                title: "Third Officer",
                description:
                  "Membantu dalam watch navigasi, perawatan peralatan keselamatan, dan alat penyelamat jiwa.",
                requirement1: "Lisensi Officer of the Watch",
                requirement2: "Pelatihan dasar STCW",
                requirement3: "Sertifikat pemadam kebakaran",
                requirement4: "Posisi entry-level",
              },
              ratings: {
                rank: "Deck Ratings",
                salary: "$1,200 - $2,500",
                title: "Kru Deck (AB/OS)",
                description:
                  "Able Seamen dan Ordinary Seamen bertanggung jawab atas pemeliharaan deck, operasi mooring, dan tugas watch.",
                requirement1: "Sertifikat AB atau OS",
                requirement2: "Pelatihan keselamatan dasar STCW",
                requirement3: "Pengalaman sea service",
                requirement4: "Kebugaran fisik",
              },
            },
            skills: {
              navigation: "Navigasi & Chart Work",
              cargo: "Operasi Kargo",
              safety: "Manajemen Keselamatan",
              maintenance: "Pemeliharaan Deck",
              communication: "Komunikasi GMDSS",
              mooring: "Operasi Mooring",
            },
          },
          engine: {
            subtitle: "Propulsi & Mesin",
            title: "Departemen <span class='highlight'>Mesin</span>",
            description:
              "Insinyur dan teknisi terampil yang bertanggung jawab atas sistem propulsi, mesin bantu, dan sistem elektrik.",
            positions: {
              chiefEngineer: {
                rank: "Chief Engineer",
                salary: "$6,000 - $12,000",
                title: "Chief Engineer",
                description:
                  "Tanggung jawab keseluruhan operasi departemen mesin, pemeliharaan, dan manajemen teknis.",
                requirement1: "Lisensi Chief Engineer (Unlimited)",
                requirement2: "Minimal 5 tahun sebagai Second Engineer",
                requirement3: "Pengalaman manajemen",
                requirement4: "Keterampilan penganggaran",
              },
              secondEngineer: {
                rank: "Second Engineer",
                salary: "$4,500 - $8,000",
                title: "Second Engineer",
                description:
                  "Bertanggung jawab atas mesin utama, perencanaan pemeliharaan, dan supervisi teknis.",
                requirement1: "Lisensi Second Engineer",
                requirement2: "Minimal 3 tahun sebagai Engineer",
                requirement3: "Pengalaman perencanaan pemeliharaan",
                requirement4: "Keterampilan supervisi teknis",
              },
              thirdEngineer: {
                rank: "Third Engineer",
                salary: "$3,000 - $5,000",
                title: "Third Engineer",
                description:
                  "Bertanggung jawab atas mesin bantu, boiler, dan pemeliharaan sistem elektrik.",
                requirement1: "Officer of the Watch (Engine)",
                requirement2: "Pengalaman sistem bantu",
                requirement3: "Pengetahuan elektrik",
                requirement4: "Keterampilan troubleshooting",
              },
              fourthEngineer: {
                rank: "Fourth Engineer",
                salary: "$2,500 - $4,000",
                title: "Fourth Engineer",
                description:
                  "Petugas engineering entry-level yang membantu dalam tugas watchkeeping dan pemeliharaan.",
                requirement1: "Officer of the Watch (Engine)",
                requirement2: "Fresh graduate diterima",
                requirement3: "Pengetahuan engineering dasar",
                requirement4: "Kemauan belajar",
              },
              ratings: {
                rank: "Engine Ratings",
                salary: "$1,200 - $2,500",
                title: "Kru Ruang Mesin",
                description:
                  "Motormen, oilers, dan wipers bertanggung jawab atas operasi ruang mesin dan dukungan pemeliharaan.",
                requirement1: "Sertifikat Engine Room Rating",
                requirement2: "Pelatihan keselamatan dasar STCW",
                requirement3: "Bakat mekanik",
                requirement4: "Kebugaran fisik",
              },
            },
            skills: {
              mainEngine: "Operasi Mesin Utama",
              electrical: "Sistem Elektrik",
              maintenance: "Pemeliharaan Preventif",
              lubrication: "Sistem Pelumasan",
              boilers: "Operasi Boiler",
              pneumatic: "Sistem Pneumatik",
            },
          },
          hotel: {
            subtitle: "Hospitality & Layanan",
            title: "Departemen <span class='highlight'>Hotel</span>",
            description:
              "Profesional hospitality yang memberikan layanan tamu luar biasa, pengalaman kuliner, dan manajemen akomodasi.",
            positions: {
              hotelManager: {
                rank: "Hotel Manager",
                salary: "$4,000 - $8,000",
                title: "Hotel Manager",
                description:
                  "Tanggung jawab keseluruhan operasi hotel, layanan tamu, dan manajemen departemen.",
                requirement1: "Gelar manajemen hospitality",
                requirement2: "Minimal 5 tahun pengalaman hotel",
                requirement3: "Keterampilan kepemimpinan dan manajemen",
                requirement4: "Keunggulan layanan pelanggan",
              },
              executiveChef: {
                rank: "Executive Chef",
                salary: "$3,500 - $6,500",
                title: "Executive Chef",
                description:
                  "Kepala departemen kuliner, perencanaan menu, kontrol kualitas makanan, dan manajemen dapur.",
                requirement1: "Gelar seni kuliner",
                requirement2: "Minimal 8 tahun pengalaman dapur",
                requirement3: "Keahlian perencanaan menu",
                requirement4: "Sertifikasi keamanan pangan",
              },
              restaurantManager: {
                rank: "Restaurant Manager",
                salary: "$2,500 - $4,500",
                title: "Restaurant Manager",
                description:
                  "Mengelola operasi restoran, kualitas layanan, pelatihan staf, dan kepuasan tamu.",
                requirement1: "Hospitality atau gelar terkait",
                requirement2: "Minimal 3 tahun pengalaman F&B",
                requirement3: "Keterampilan pelatihan layanan",
                requirement4: "Multilingual lebih disukai",
              },
              housekeepingSupervisor: {
                rank: "Housekeeping Supervisor",
                salary: "$1,800 - $3,000",
                title: "Housekeeping Supervisor",
                description:
                  "Mengawasi pembersihan kabin, operasi laundry, dan pemeliharaan akomodasi.",
                requirement1: "Pengalaman housekeeping",
                requirement2: "Keterampilan supervisi",
                requirement3: "Manajemen inventaris",
                requirement4: "Perhatian terhadap detail",
              },
              stewards: {
                rank: "Stewards",
                salary: "$1,200 - $2,200",
                title: "Stewards / Wait Staff",
                description:
                  "Menyediakan layanan makanan dan minuman, layanan kabin, dan pembersihan area publik.",
                requirement1: "Ijazah SMA",
                requirement2: "Pengalaman industri layanan",
                requirement3: "Keterampilan komunikasi baik",
                requirement4: "Berorientasi layanan pelanggan",
              },
            },
            skills: {
              fnb: "Layanan Makanan & Minuman",
              housekeeping: "Housekeeping Unggul",
              guest: "Hubungan Tamu",
              butler: "Layanan Butler",
              culinary: "Seni Kuliner",
              language: "Keterampilan Multibahasa",
            },
          },
          entertainment: {
            subtitle: "Aktivitas & Hiburan Tamu",
            title: "Departemen <span class='highlight'>Hiburan</span>",
            description:
              "Penampil berbakat, koordinator aktivitas, dan profesional hiburan yang menciptakan pengalaman tamu yang berkesan.",
            positions: {
              cruiseDirector: {
                rank: "Cruise Director",
                salary: "$4,000 - $7,000",
                title: "Cruise Director",
                description:
                  "Mengawasi semua program hiburan, aktivitas, dan inisiatif keterlibatan tamu.",
                requirement1: "Gelar manajemen hiburan",
                requirement2: "Minimal 5 tahun pengalaman",
                requirement3: "Keterampilan public speaking",
                requirement4: "Keahlian perencanaan acara",
              },
              entertainers: {
                rank: "Entertainers",
                salary: "$2,000 - $5,000",
                title: "Penampil & Entertainers",
                description:
                  "Penyanyi, penari, musisi, dan artis varietas yang memberikan pertunjukan panggung dan hiburan.",
                requirement1: "Latar belakang pertunjukan",
                requirement2: "Audisi diperlukan",
                requirement3: "Stage presence",
                requirement4: "Keserbagunaan dalam pertunjukan",
              },
              activityCoordinator: {
                rank: "Activity Coordinator",
                salary: "$1,800 - $3,000",
                title: "Activity Coordinator",
                description:
                  "Merencanakan dan memandu aktivitas tamu, permainan, lokakarya, dan acara sosial.",
                requirement1: "Gelar rekreasi atau terkait",
                requirement2: "Pengalaman perencanaan acara",
                requirement3: "Kepribadian supel",
                requirement4: "Multilingual lebih disukai",
              },
              youthStaff: {
                rank: "Youth Staff",
                salary: "$1,500 - $2,500",
                title: "Staf Program Pemuda",
                description:
                  "Menyediakan perawatan anak, aktivitas pemuda, dan program pendidikan untuk tamu yang lebih muda.",
                requirement1: "Latar belakang pendidikan atau perawatan anak",
                requirement2: "Sertifikasi keselamatan anak",
                requirement3: "Perencanaan aktivitas kreatif",
                requirement4: "Kesabaran dan antusiasme",
              },
            },
            skills: {
              performing: "Pertunjukan Panggung",
              games: "Pemandu Permainan",
              engagement: "Keterlibatan Tamu",
              music: "Bakat Musik",
              creative: "Seni Kreatif",
              language: "MC Multibahasa",
            },
          },
          skillsTitle: "Keterampilan & Kompetensi Utama",
          recruitment: {
            subtitle: "Bergabung dengan Tim Kami",
            title: "Proses <span class='highlight'>Rekrutmen</span>",
            description:
              "Proses rekrutmen terstruktur kami memastikan kami memilih kandidat terbaik untuk setiap posisi.",
            step1: {
              title: "Pengiriman Aplikasi",
              description:
                "Kirimkan aplikasi Anda melalui portal online kami dengan dokumen yang diperlukan.",
              detail1: "Lengkapi formulir aplikasi online",
              detail2: "Unggah CV/resume",
              detail3: "Kirim salinan sertifikat",
              detail4: "Berikan foto ukuran paspor",
            },
            step2: {
              title: "Penyaringan Dokumen",
              description:
                "Tim rekrutmen kami meninjau semua aplikasi dan dokumen.",
              detail1: "Verifikasi sertifikat",
              detail2: "Validasi pengalaman",
              detail3: "Pemeriksaan kesehatan",
              detail4: "Penyaringan latar belakang",
            },
            step3: {
              title: "Wawancara & Penilaian",
              description:
                "Kandidat terpilih menjalani wawancara dan penilaian keterampilan.",
              detail1: "Wawancara teknis",
              detail2: "Wawancara HR",
              detail3: "Penilaian praktis",
              detail4: "Evaluasi psikologis",
            },
            step4: {
              title: "Pelatihan & Sertifikasi",
              description: "Penyelesaian pelatihan dan sertifikasi yang diperlukan.",
              detail1: "Pelatihan keselamatan dasar STCW",
              detail2: "Pelatihan khusus perusahaan",
              detail3: "Pelatihan kesadaran keamanan",
              detail4: "Pemeriksaan medis",
            },
            step5: {
              title: "Pengerahan",
              description:
                "Penempatan akhir dan pengaturan perjalanan untuk bergabung dengan kapal.",
              detail1: "Penandatanganan kontrak",
              detail2: "Pengaturan perjalanan",
              detail3: "Pemrosesan visa",
              detail4: "Instruksi bergabung",
            },
            cta: {
              title: "Siap Memulai Karir Maritim Anda?",
              description:
                "Daftar sekarang dan bergabung dengan tim pelaut profesional kami. Kami menawarkan paket kompetitif dan peluang pertumbuhan karir.",
            },
          },
          documents: {
            subtitle: "Siapkan Aplikasi Anda",
            title: "Dokumen yang <span class='highlight'>Diperlukan</span>",
            category1: {
              title: "Dokumen Pribadi",
              item1: "Paspor (validitas minimal 2 tahun)",
              item2: "Dokumen Identitas Pelaut (SID)",
              item3: "Akte kelahiran",
              item4: "Surat nikah (jika ada)",
              item5: "Surat Keterangan Catatan Kepolisian (SKCK)",
            },
            category2: {
              title: "Dokumen Profesional",
              item1: "Sertifikat Kompetensi (COC)",
              item2: "Sertifikat STCW",
              item3: "Sertifikat pelatihan",
              item4: "Catatan layanan laut",
              item5: "Surat rekomendasi",
            },
            category3: {
              title: "Medis & Kesehatan",
              item1: "Sertifikat kesehatan medis valid",
              item2: "Catatan vaksinasi",
              item3: "Sertifikat gigi",
              item4: "Laporan pemeriksaan mata",
              item5: "Sertifikat vaksinasi COVID-19",
            },
          },
        },

        // Contact Form Section
        contact: {
          form: {
            subtitle: "Kirim Pesan",
            title: "Formulir <span class='highlight'>Kontak</span>",
            description: "Isi formulir di bawah ini dengan lengkap dan tim kami akan menghubungi Anda dalam waktu 2 jam.",
            step1: "Informasi Dasar",
            step2: "Detail Layanan",
            step3: "Konfirmasi",
            step1Title: "Informasi Kontak Anda",
            step1Desc: "Silakan lengkapi informasi kontak Anda",
            step2Title: "Kebutuhan Layanan Anda",
            step2Desc: "Pilih layanan yang Anda butuhkan",
            step3Title: "Detail Pesan & Lampiran",
            step3Desc: "Tambahkan pesan dan lampiran jika diperlukan",
            fullName: "Nama Lengkap",
            email: "Email",
            phone: "Nomor Telepon/WhatsApp",
            company: "Nama Perusahaan",
            position: "Jabatan",
            companyType: "Jenis Perusahaan",
            serviceType: "Jenis Layanan yang Dibutuhkan",
            vesselType: "Jenis Kapal",
            urgency: "Tingkat Urgensi",
            crewSize: "Jumlah Kru yang Dibutuhkan",
            budgetRange: "Range Budget",
            subject: "Subjek Pesan",
            message: "Detail Permintaan",
            attachment: "Lampiran File (Opsional)",
            referral: "Darimana Anda mengetahui kami?",
            prev: "Sebelumnya",
            next: "Selanjutnya",
            submit: "Kirim Permintaan",
            successTitle: "Permintaan Terkirim!",
            successMsg: "Terima kasih atas permintaan Anda. Tim kami akan menghubungi Anda dalam waktu 2 jam melalui email dan telepon yang telah Anda berikan.",
            requestId: "ID Permintaan:",
            newRequest: "Ajukan Permintaan Baru",
          },
        },
      },

      zh: {
        // Meta & Page Titles
        title: "PT Indo OceanCrew Services - 专业船员代理及船舶供应商",
        tagline: "专业海事解决方案",

        // Navigation
        nav: {
          home: "首页",
          about: "关于我们",
          services: "服务项目",
          departments: "船员服务",
          contact: "联系我们",
          contactBtn: "联系我们",
          crewing: "船员代理服务",
          chandler: "船舶供应商",
          documentation: "船舶文件服务",
          logistics: "海事物流",
          crew: "我们的船员",
        },

        // Hero Section
        hero: {
          subtitle: "专业船员代理及船舶供应商服务",
          title: "<span class='highlight'>跨越海洋</span>的海事卓越",
          description:
            "值得信赖的海事服务提供商，专注于船员管理和船舶供应解决方案。确保每艘船舶安全、高效、顺畅地运营。",
          servicesBtn: "我们的服务",
          contactBtn: "获取报价",
          subtitle2: "全球海事解决方案",
          description2:
            "全面的海事服务，符合国际标准（MLC、STCW、ISM Code），以诚信、可靠和卓越为宗旨。",
        },

        // Services Highlights
        services: {
          highlight1: {
            title: "船员代理服务",
            desc: "为所有船舶类型提供专业的船员管理和招聘解决方案",
          },
          highlight2: {
            title: "船舶供应商服务",
            desc: "为全球船队提供优质的船舶物资和供应解决方案",
          },
          highlight3: {
            title: "船舶文件服务",
            desc: "支持符合国际海事法规的合规服务",
          },
          learnMore: "了解更多",
          crewing: {
            subtitle: "专业船员管理",
            title: "船员代理<span class='highlight'>服务</span>",
            description:
              "面向各类船舶的综合船员管理解决方案，确保合格且持证的船员，以实现安全高效的海事运营。",
            applyNow: "立即申请",
            requirements: "要求：",
            certificates: {
              master: "船长证书 (Master License)",
              stcw: "STCW证书",
              gmdss: "GMDSS证书",
              chiefMate: "大副证书 (Chief Mate License)",
              bulk: "散货船经验",
              oow: "值班驾驶员证书 (OOW)",
              ecdis: "电子海图显示与信息系统 (ECDIS)",
              ff: "消防证书",
              ab: "水手证书 (AB)",
              pscrb: "精通救生艇筏 (PSCRB)",
              chiefEng: "轮机长证书",
              management: "管理级",
              secondEng: "大管轮证书",
              maintenance: "维护保养",
              oowEng: "值班轮机员证书 (OOW Engine)",
              electrical: "电气知识",
              basic: "基本安全培训",
              erRating: "机舱值班证书 (ER Rating)",
              welding: "焊接技能",
              hospitality: "酒店管理",
              foodSafety: "食品安全",
              leadership: "领导力",
              culinary: "烹饪艺术",
              haccp: "HACCP认证",
              fnb: "餐饮管理",
              wine: "酒类服务",
              service: "卓越服务",
              housekeeping: "客房服务",
              cleaning: "清洁程序",
              supervisory: "监督管理",
              entertainment: "娱乐管理",
              mc: "司仪 (MC)",
              performing: "表演艺术",
              music: "音乐/舞蹈",
              acting: "表演",
              recreation: "休闲管理",
              event: "活动策划",
              firstAid: "急救",
              childcare: "儿童保育",
              education: "教育背景",
            },
            nav: {
              deck: "甲板部门",
              engine: "轮机部门",
              hotel: "酒店部门",
              entertainment: "娱乐部门",
              recruitment: "招聘流程",
            },
            deck: {
              subtitle: "航行与操作",
              title: "甲板<span class='highlight'>部门</span>",
              description:
                "专业的甲板高级船员和普通船员，负责航行、货物操作和船舶安全。",
              positions: {
                master: {
                  rank: "Master / 船长",
                  salary: "$5,000 - $12,000",
                  title: "船长 (Master)",
                  description:
                    "船舶的总指挥，负责安全、航行及遵守国际法规。",
                  requirement1: "船长证书 (无限航区)",
                  requirement2: "至少5年大副经验",
                  requirement3: "STCW认证",
                  requirement4: "有效健康证",
                },
                chiefOfficer: {
                  rank: "大副",
                  salary: "$4,000 - $8,000",
                  title: "大副 (Chief Officer)",
                  description:
                    "副指挥，负责货物操作、稳性和甲板部管理。",
                  requirement1: "大副证书",
                  requirement2: "至少3年高级船员经验",
                  requirement3: "货物处理经验",
                  requirement4: "领导能力",
                },
                secondOfficer: {
                  rank: "二副",
                  salary: "$3,000 - $5,000",
                  title: "二副 (Second Officer)",
                  description:
                    "负责航行、海图更正和通讯设备维护。",
                  requirement1: "值班驾驶员证书",
                  requirement2: "航行经验",
                  requirement3: "GMDSS证书",
                  requirement4: "健康适任",
                },
                thirdOfficer: {
                  rank: "三副",
                  salary: "$2,500 - $4,000",
                  title: "三副 (Third Officer)",
                  description:
                    "协助航行值班、救生设备维护和救生设备。",
                  requirement1: "值班驾驶员证书",
                  requirement2: "STCW基本培训",
                  requirement3: "消防证书",
                  requirement4: "入门级职位",
                },
                ratings: {
                  rank: "甲板普通船员",
                  salary: "$1,200 - $2,500",
                  title: "甲板部水手 (AB/OS)",
                  description:
                    "负责甲板维护、系泊操作和值班职责的熟练水手和普通水手。",
                  requirement1: "AB或OS证书",
                  requirement2: "STCW基本安全培训",
                  requirement3: "海上服务经验",
                  requirement4: "身体健康",
                },
              },
              skills: {
                navigation: "航行与海图作业",
                cargo: "货物操作",
                safety: "安全管理",
                maintenance: "甲板维护",
                communication: "GMDSS通讯",
                mooring: "系泊操作",
              },
            },
            engine: {
              subtitle: "推进与机械",
              title: "轮机<span class='highlight'>部门</span>",
              description:
                "负责推进系统、辅助机械和电气系统的熟练工程师和技术人员。",
              positions: {
                chiefEngineer: {
                  rank: "轮机长",
                  salary: "$6,000 - $12,000",
                  title: "轮机长 (Chief Engineer)",
                  description:
                    "全面负责轮机部运营、维护和技术管理。",
                  requirement1: "轮机长证书 (无限功率)",
                  requirement2: "至少5年大管轮经验",
                  requirement3: "管理经验",
                  requirement4: "预算技能",
                },
                secondEngineer: {
                  rank: "大管轮",
                  salary: "$4,500 - $8,000",
                  title: "大管轮 (Second Engineer)",
                  description:
                    "负责主机、维护计划和技术监督。",
                  requirement1: "大管轮证书",
                  requirement2: "至少3年工程师经验",
                  requirement3: "维护计划经验",
                  requirement4: "技术监督技能",
                },
                thirdEngineer: {
                  rank: "二管轮",
                  salary: "$3,000 - $5,000",
                  title: "二管轮 (Third Engineer)",
                  description:
                    "负责辅机、锅炉和电气系统维护。",
                  requirement1: "值班轮机员证书",
                  requirement2: "辅助系统经验",
                  requirement3: "电气知识",
                  requirement4: "故障排除技能",
                },
                fourthEngineer: {
                  rank: "三管轮",
                  salary: "$2,500 - $4,000",
                  title: "三管轮 (Fourth Engineer)",
                  description:
                    "协助值班和维护职责的入门级工程官员。",
                  requirement1: "值班轮机员证书",
                  requirement2: "接受应届毕业生",
                  requirement3: "基本工程知识",
                  requirement4: "学习意愿",
                },
                ratings: {
                  rank: "机舱普通船员",
                  salary: "$1,200 - $2,500",
                  title: "机舱人员 (Motorman/Oiler)",
                  description:
                    "负责机舱操作和维护支持的机工、加油工和擦拭工。",
                  requirement1: "机舱值班证书",
                  requirement2: "STCW基本安全培训",
                  requirement3: "机械天赋",
                  requirement4: "身体健康",
                },
              },
              skills: {
                mainEngine: "主机操作",
                electrical: "电气系统",
                maintenance: "预防性维护",
                lubrication: "润滑系统",
                boilers: "锅炉操作",
                pneumatic: "气动系统",
              },
            },
            hotel: {
              subtitle: "酒店与服务",
              title: "酒店<span class='highlight'>部门</span>",
              description:
                "提供卓越宾客服务、烹饪体验和住宿管理的酒店专业人员。",
              positions: {
                hotelManager: {
                  rank: "酒店经理",
                  salary: "$4,000 - $8,000",
                  title: "酒店经理",
                  description:
                    "全面负责酒店运营、宾客服务和部门管理。",
                  requirement1: "酒店管理学位",
                  requirement2: "至少5年酒店经验",
                  requirement3: "领导和管理能力",
                  requirement4: "卓越客户服务",
                },
                executiveChef: {
                  rank: "行政总厨",
                  salary: "$3,500 - $6,500",
                  title: "行政总厨",
                  description:
                    "烹饪部门主管，负责菜单规划、食品质量控制和厨房管理。",
                  requirement1: "烹饪艺术学位",
                  requirement2: "至少8年厨房经验",
                  requirement3: "菜单规划专长",
                  requirement4: "食品安全认证",
                },
                restaurantManager: {
                  rank: "餐厅经理",
                  salary: "$2,500 - $4,500",
                  title: "餐厅经理",
                  description:
                    "管理餐厅运营、服务质量、员工培训和宾客满意度。",
                  requirement1: "酒店或相关学位",
                  requirement2: "至少3年餐饮经验",
                  requirement3: "服务培训技能",
                  requirement4: "多语言优先",
                },
                housekeepingSupervisor: {
                  rank: "客房主管",
                  salary: "$1,800 - $3,000",
                  title: "客房主管",
                  description:
                    "监督客房清洁、洗衣运营和住宿维护。",
                  requirement1: "客房服务经验",
                  requirement2: "监督技能",
                  requirement3: "库存管理",
                  requirement4: "注重细节",
                },
                stewards: {
                  rank: "服务员",
                  salary: "$1,200 - $2,200",
                  title: "服务员 / 侍者",
                  description:
                    "提供餐饮服务、客房服务和公共区域清洁。",
                  requirement1: "高中文凭",
                  requirement2: "服务行业经验",
                  requirement3: "良好的沟通技巧",
                  requirement4: "客户服务导向",
                },
              },
              skills: {
                fnb: "餐饮服务",
                housekeeping: "卓越客房服务",
                guest: "宾客关系",
                butler: "管家服务",
                culinary: "烹饪艺术",
                language: "多语言技能",
              },
            },
            entertainment: {
              subtitle: "宾客活动与娱乐",
              title: "娱乐<span class='highlight'>部门</span>",
              description:
                "才华横溢的表演者、活动协调员和娱乐专业人士，创造难忘的宾客体验。",
              positions: {
                cruiseDirector: {
                  rank: "邮轮总监",
                  salary: "$4,000 - $7,000",
                  title: "邮轮总监",
                  description:
                    "监督所有娱乐节目、活动和宾客参与计划。",
                  requirement1: "娱乐管理学位",
                  requirement2: "至少5年经验",
                  requirement3: "公开演讲技巧",
                  requirement4: "活动策划专长",
                },
                entertainers: {
                  rank: "演艺人员",
                  salary: "$2,000 - $5,000",
                  title: "表演者与演艺人员",
                  description:
                    "提供舞台表演和娱乐的歌手、舞者、音乐家和综艺艺术家。",
                  requirement1: "表演背景",
                  requirement2: "需试镜",
                  requirement3: "舞台表现力",
                  requirement4: "表演多面性",
                },
                activityCoordinator: {
                  rank: "活动协调员",
                  salary: "$1,800 - $3,000",
                  title: "活动协调员",
                  description:
                    "策划并主持宾客活动、游戏、讲习班和社交活动。",
                  requirement1: "休闲或相关学位",
                  requirement2: "活动策划经验",
                  requirement3: "性格外向",
                  requirement4: "多语言优先",
                },
                youthStaff: {
                  rank: "青少年员工",
                  salary: "$1,500 - $2,500",
                  title: "青少年项目员工",
                  description:
                    "为年轻宾客提供儿童保育、青年活动和教育项目。",
                  requirement1: "教育或儿童保育背景",
                  requirement2: "儿童安全认证",
                  requirement3: "创意活动策划",
                  requirement4: "耐心和热情",
                },
              },
              skills: {
                performing: "舞台表演",
                games: "游戏主持",
                engagement: "宾客互动",
                music: "音乐才华",
                creative: "创意艺术",
                language: "多语言主持",
              },
            },
            skillsTitle: "关键技能与能力",
            feature1: {
              title: "船员招聘与选拔",
              description:
                "综合招聘流程，包括筛选、面试、背景调查和合格海员选拔。",
              item1: "候选人搜寻与筛选",
              item2: "技术和心理测试",
              item3: "体检和体能测试",
              item4: "文件验证和认证",
            },
            feature2: {
              title: "培训与认证",
              description: "符合国际标准的专业海事培训计划和认证管理。",
              item1: "STCW培训和认证",
              item2: "安全和应急程序培训",
              item3: "特定船舶类型培训",
              item4: "持续专业发展",
            },
            feature3: {
              title: "旅行与物流管理",
              description: "为船员派遣和遣返提供完整的旅行安排和物流支持。",
              item1: "航班和住宿安排",
              item2: "签证和移民协助",
              item3: "机场接送和地面交通",
              item4: "紧急旅行协调",
            },
            feature4: {
              title: "船员福利与支持",
              description:
                "为船员提供7x24小时支持服务，确保其福祉并及时解决问题。",
              item1: "7x24小时紧急支持",
              item2: "合同管理和行政",
              item3: "家庭沟通协助",
              item4: "医疗和保险协调",
            },
            processTitle: "我们的船员服务流程",
            step1: {
              title: "需求分析",
              description: "了解客户需求和船舶要求",
            },
            step2: {
              title: "候选人选拔",
              description: "筛选和选拔合格候选人",
            },
            step3: {
              title: "文件与培训",
              description: "认证和部署前培训",
            },
            step4: {
              title: "部署与支持",
              description: "旅行安排和持续支持",
            },
            recruitment: {
              subtitle: "加入我们的团队",
              title: "招聘<span class='highlight'>流程</span>",
              description:
                "我们结构化的招聘流程确保为每个职位选择最佳候选人。",
              step1: {
                title: "提交申请",
                description:
                  "通过我们的在线门户提交申请及所需文件。",
                detail1: "填写在线申请表",
                detail2: "上传简历/CV",
                detail3: "提交证书副本",
                detail4: "提供护照尺寸照片",
              },
              step2: {
                title: "文件筛选",
                description:
                  "我们的招聘团队审查所有申请和文件。",
                detail1: "证书验证",
                detail2: "经验核实",
                detail3: "体检检查",
                detail4: "背景调查",
              },
              step3: {
                title: "面试与评估",
                description:
                  "入围候选人接受面试和技能评估。",
                detail1: "技术面试",
                detail2: "HR面试",
                detail3: "实操评估",
                detail4: "心理评估",
              },
              step4: {
                title: "培训与认证",
                description: "完成所需的培训和认证。",
                detail1: "STCW基本安全培训",
                detail2: "公司特定培训",
                detail3: "保安意识培训",
                detail4: "体格检查",
              },
              step5: {
                title: "派遣",
                description:
                  "最终安置和上船旅行安排。",
                detail1: "签署合同",
                detail2: "旅行安排",
                detail3: "签证办理",
                detail4: "上船须知",
              },
              cta: {
                title: "准备好开始您的海事职业了吗？",
                description:
                  "立即申请并加入我们的专业海员团队。我们提供具有竞争力的待遇和职业发展机会。",
              },
            },
            documents: {
              subtitle: "准备您的申请",
              title: "所需<span class='highlight'>文件</span>",
              category1: {
                title: "个人文件",
                item1: "护照 (至少2年有效期)",
                item2: "海员身份证件 (SID)",
                item3: "出生证明",
                item4: "结婚证 (如适用)",
                item5: "无犯罪记录证明",
              },
              category2: {
                title: "专业文件",
                item1: "适任证书 (COC)",
                item2: "STCW证书",
                item3: "培训证书",
                item4: "服务资历簿",
                item5: "推荐信",
              },
              category3: {
                title: "医疗与健康",
                item1: "有效健康适任证书",
                item2: "疫苗接种记录",
                item3: "牙科证明",
                item4: "视力检查报告",
                item5: "COVID-19疫苗证书",
              },
            },
          },
          chandler: {
            subtitle: "优质海事物资",
            title: "船舶供应商<span class='highlight'>服务</span>",
            description:
              "全面的船舶供应解决方案，为全球运营的船舶提供优质的食品、物料和设备。",
            supplyTitle: "完整的船舶供应",
            supplyDescription:
              "我们要提供完整的船舶供应服务，包括食品供应、技术物料、甲板和机舱物料以及保税品。",
            category1: {
              title: "食品与供应",
              item1: "新鲜水果和蔬菜",
              item2: "冷冻肉类和海鲜",
              item3: "干货和杂货",
              item4: "饮料和乳制品",
            },
            category2: {
              title: "技术物料",
              item1: "发动机备件",
              item2: "电气组件",
              item3: "焊接用品",
              item4: "工具和设备",
            },
            category3: {
              title: "甲板与保税品",
              item1: "甲板物料和设备",
              item2: "安全设备",
              item3: "保税品",
              item4: "化学品和润滑油",
            },
            captionTitle: "质量保证供应",
            captionText: "所有供应均符合国际质量标准并按时交付。",
            highlight1: {
              title: "7x24小时服务可用性",
              description: "全天候服务以满足紧急船舶需求",
            },
            highlight2: {
              title: "质量认证供应",
              description: "所有供应均符合国际质量和安全标准",
            },
            highlight3: {
              title: "全球网络覆盖",
              description: "主要港口的供应能力",
            },
          },
          documentation: {
            subtitle: "法规遵从",
            title: "船舶文件<span class='highlight'>服务</span>",
            description:
              "全面的文件支持，确保船舶符合国际海事法规和船旗国要求。",
            service1: {
              title: "船舶注册与许可",
              item1: "初始船舶注册",
              item2: "船旗国文件",
              item3: "港口国监督合规",
              item4: "许可和许可证",
            },
            service2: {
              title: "证书管理",
              item1: "SOLAS证书",
              item2: "MARPOL文件",
              item3: "ISM/ISPS证书",
              item4: "船级社证书",
            },
            service3: {
              title: "船员文件",
              item1: "海员就业协议",
              item2: "适任证书",
              item3: "健康证书",
              item4: "培训记录管理",
            },
            complianceTitle: "合规管理时间表",
            timeline1: {
              date: "初始",
              title: "文件审计",
              description: "现有文件的全面审查",
            },
            timeline2: {
              date: "2周",
              title: "差距分析",
              description: "识别合规要求",
            },
            timeline3: {
              date: "4周",
              title: "文件准备",
              description: "准备所需证书和文件",
            },
            timeline4: {
              date: "持续",
              title: "续期管理",
              description: "持续监控和更新即将到期的文件",
            },
          },
          logistics: {
            subtitle: "供应链管理",
            title: "海事物流<span class='highlight'>服务</span>",
            description:
              "海事运营的端到端物流解决方案，确保物资和设备及时交付给全球船舶。",
            service1: {
              title: "港口物流",
              description: "港口运营、货物处理和仓储解决方案的协调",
              item1: "港口代理服务",
              item2: "货物处理协调",
              item3: "报关协助",
              item4: "仓储和储存",
            },
            service2: {
              title: "供应链管理",
              description: "从采购到交付的完整供应链解决方案",
              item1: "供应商采购和管理",
              item2: "库存管理",
              item3: "订单处理和跟踪",
              item4: "准时制交付协调",
            },
            service3: {
              title: "紧急物流",
              description: "针对紧急船舶需求的快速响应物流",
              item1: "紧急备件交付",
              item2: "医疗物资运输",
              item3: "技术支持设备交付",
              item4: "危机管理物流",
            },
          },
          technical: {
            subtitle: "技术支持",
            title: "技术与工程<span class='highlight'>服务</span>",
            description:
              "为船舶维护、维修和运营效率提供全面的技术支持和工程服务。",
            service1: {
              title: "船舶维护支持",
              description: "预防性和纠正性维护计划和执行",
              feature1: "维护计划",
              feature2: "备件管理",
              feature3: "技术监督",
              feature4: "质量控制",
            },
            service2: {
              title: "技术咨询",
              description: "海事运营的专家技术建议和解决方案",
              feature1: "运营效率",
              feature2: "燃料优化",
              feature3: "设备选择",
              feature4: "合规咨询",
            },
          },
        },

        // About Preview
        about: {
          subtitle: "关于我们公司",
          title: "您值得信赖的<span class='highlight'>海事合作伙伴</span>",
          description1:
            "PT Indo OceanCrew Services 是一家印度尼西亚海事公司，为全球航运业提供合格的船员和全面的船舶供应解决方案。",
          description2:
            "作为值得信赖的船员代理和船舶供应商，我们提供端到端服务——从船员招聘和派遣到船舶物资供应和物流。",
          feature1: "符合MLC、STCW、ISM标准",
          feature2: "全球网络",
          feature3: "24/7支持",
          feature4: "质量保证",
          learnMoreBtn: "了解更多关于我们",
          experience: "年经验",
        },

        // Core Services
        coreServices: {
          subtitle: "我们提供的服务",
          title: "全面的<span class='highlight'>海事解决方案</span>",
          description:
            "凭借卓越的执行力和创新的海事解决方案，我们致力于为合作伙伴提供卓越价值。",
          service1: {
            title: "船员代理服务",
            description:
              "我们为各类船舶提供专业的船员管理和招聘解决方案。我们的认证船员经过培训，符合国际标准。",
            feature1: "船员招聘与选拔",
            feature2: "培训与认证",
            feature3: "旅行与物流",
            feature4: "船员福利管理",
          },
          service2: {
            title: "船东合作服务",
            description:
              "船员与船东之间的紧密合作对于确保提供高质量、标准化的服务至关重要。",
            feature1: "战略伙伴关系",
            feature2: "性能监控",
            feature3: "合规保证",
            feature4: "持续改进",
          },
          service3: {
            title: "船舶文件服务",
            description:
              "我们提供全面的船舶文件支持，帮助船东和运营商维持符合本地和国际海事法规的要求。",
            feature1: "注册与许可",
            feature2: "证书管理",
            feature3: "合规文件",
            feature4: "审计准备",
          },
        },

        // Statistics
        stats: {
          crew: "认证船员",
          vessels: "服务船舶",
          countries: "覆盖国家",
          support: "24/7支持",
        },

        // CTA Section
        cta: {
          title: "准备好与海事专家合作了吗？",
          description:
            "联系我们，获取可靠的船员管理、船舶供应解决方案和符合全球标准的全面海事服务。",
          contactBtn: "立即联系我们",
        },

        // Footer
        footer: {
          tagline: "专业海事解决方案",
          description:
            "领先的海事服务提供商，专注于全球航运业的船员管理和船舶供应解决方案。",
          services: "服务项目",
          company: "公司信息",
          contact: "联系我们",
          careers: "职业发展",
          blog: "博客",
          address: "Menara Cakrawala lt 15 no 1506 jl M.H. Thamrin Kec. Menteng, Kota Jakarta Pusat 10340",
          hours: "周一至周五：8:00 - 17:00",
          rights: "保留所有权利。",
          privacy: "隐私政策",
          terms: "服务条款",
          cookies: "Cookie政策",
        },

        // Services Page
        servicesPage: {
          title: "我们的服务 - PT Indo OceanCrew Services",
          heroTitle: "我们的专业服务",
          heroSubtitle:
            "为全球航运运营提供全面的海事解决方案，确保安全、效率和合规性。",
        },
        servicesNav: {
          crewing: "船员代理服务",
          chandler: "船舶供应商",
          documentation: "船舶文件服务",
          logistics: "海事物流",
          technical: "技术服务",
        },

        // About Page
        aboutPage: {
          title: "关于我们 - PT Indo OceanCrew Services",
          heroTitle: "关于我们公司",
          heroSubtitle:
            "专业的海事服务提供商，在船员管理和船舶供应商服务方面拥有多年经验。",
          overviewSubtitle: "公司概览",
          overviewTitle:
            "您值得信赖的<span class='highlight'>海事合作伙伴</span>",
          leadText:
            "PT Indo OceanCrew Services 是一家值得信赖的海事服务提供商，专注于船员管理和船舶供应解决方案。凭借广泛的认证船员网络和优质的海上物资，我们确保我们服务的每艘船舶都能平稳、安全、高效地运营。",
          missionTitle: "我们的使命",
          missionText:
            "通过专业的船员管理和全面的船舶供应解决方案，提供卓越、可靠和全球标准，以满足航运业的动态需求。",
          visionTitle: "我们的愿景",
          visionText:
            "成为东南亚领先的海事服务提供商，以我们的诚信、专业精神和对可持续海事运营的承诺而闻名。",
          experienceText: "年",
          valuesSubtitle: "我们的原则",
          valuesTitle: "核心<span class='highlight'>价值观</span>",
          value1Title: "诚信",
          value1Desc: "我们在所有业务中以诚实、透明和道德标准开展业务。",
          value2Title: "卓越",
          value2Desc: "我们努力提供最高质量的服务，不断提高我们的标准。",
          value3Title: "合作",
          value3Desc:
            "我们与客户、船员和利益相关者建立牢固的伙伴关系，实现共同成功。",
          value4Title: "安全",
          value4Desc: "我们将船员和我们服务的船舶的安全与福祉置于首位。",
          certSubtitle: "质量保证",
          certTitle: "认证与<span class='highlight'>合规</span>",
          cert1Title: "符合MLC 2006",
          cert1Desc: "海事劳工公约标准，用于船员福利和工作条件。",
          cert2Title: "STCW认证",
          cert2Desc: "关于培训、认证和值班标准的国际公约。",
          cert3Title: "ISM规则",
          cert3Desc: "国际安全管理规则，用于船舶的安全管理和操作。",
          teamSubtitle: "认识我们的团队",
          teamTitle: "领导力与<span class='highlight'>专业知识</span>",
          team1Name: "约翰·海事",
          team1Position: "首席执行官",
          team1Bio: "在航运业拥有25年以上的经验，擅长船员管理和船舶运营。",
          team2Name: "莎拉·海洋",
          team2Position: "运营总监",
          team2Bio: "专注于海事物流和船员部署，拥有15年的行业经验。",
          team3Name: "迈克尔·船员",
          team3Position: "合规经理",
          team3Bio: "精通国际海事法规、认证和质量保证系统。",
        },

        // Contact Page
        contactPage: {
          title: "联系我们 - PT Indo OceanCrew Services",
          heroTitle: "联系我们",
          heroSubtitle:
            "准备好与海事专家合作了吗？联系我们的团队，获取可靠的船员管理和船舶供应解决方案。",
        },
        contact: {
          getQuote: "获取报价",
          support: "支持可用",
          response: "响应时间",
          satisfaction: "客户满意度",
          info: {
            subtitle: "联系我们",
            title: "联系信息",
            description:
              "通过多种渠道联系我们。我们的团队全天候为您提供海事需求支持。",
          },
          office: {
            title: "总部办公室",
            address: "Menara Cakrawala lt 15 no 1506 jl M.H. Thamrin\nKec. Menteng, Kota Jakarta Pusat 10340",
          },
          getDirections: "获取路线",
          phone: {
            title: "电话和WhatsApp",
          },
          "24hours": "24/7紧急支持",
          email: {
            title: "电子邮件",
          },
          responseTime: "< 2小时响应",
          hours: {
            title: "营业时间",
            monday: "周一至周五",
            saturday: "周六",
            emergency: "紧急情况",
            24: "24/7",
          },
          form: {
            subtitle: "发送消息",
            title: "获取您的免费报价",
            description:
              "填写下面的表格，我们的团队将在2小时内通过定制解决方案联系您，以满足您的海事需求。",
            firstName: "名字 *",
            lastName: "姓氏 *",
            email: "电子邮件地址 *",
            phone: "电话号码",
            company: "公司名称",
            service: "感兴趣的服务 *",
            selectService: "选择服务",
            other: "其他",
            subject: "主题 *",
            message: "消息 *",
            messagePlaceholder: "请详细描述您的要求...",
            newsletter: "订阅我们的新闻通讯，获取海事行业更新",
            submit: "发送消息",
          },
          quick: {
            title: "快速联系",
            description: "需要立即协助？请直接致电或发送WhatsApp消息。",
            call: "立即致电",
            whatsapp: "WhatsApp",
          },
          services: {
            title: "我们的服务",
          },
        },

        // Crewing Page
        crewingPage: {
          title: "船员服务 - PT Indo OceanCrew Services",
          heroTitle: "专业船员服务",
          heroSubtitle:
            "为所有船舶部门提供合格船员，确保全球海事运营的安全与高效。",
        },
        crewing: {
          applyNow: "立即申请",
          stats: {
            crew: "认证船员",
            vessels: "服务船舶",
            years: "年经验",
          },
          nav: {
            deck: "甲板部门",
            engine: "机舱部门",
            hotel: "酒店部门",
            entertainment: "娱乐部门",
            recruitment: "招聘流程",
          },
          requirements: "要求：",
          skillsTitle: "关键技能与能力",
          certificates: {
            master: "船长执照",
            stcw: "STCW",
            gmdss: "GMDSS",
            chiefMate: "大副执照",
            bulk: "散货船",
            oow: "OOW执照",
            ecdis: "电子海图显示与信息系统",
            ff: "消防",
            ab: "AB证书",
            pscrb: "PSCRB",
            chiefEng: "轮机长执照",
            management: "管理级",
            secondEng: "大管轮执照",
            maintenance: "维护",
            oowEng: "OOW（轮机）执照",
            electrical: "电气",
            basic: "基本培训",
            erRating: "机舱值班证书",
            welding: "焊接",
            hospitality: "酒店管理",
            foodSafety: "食品安全",
            leadership: "领导力",
            culinary: "烹饪艺术",
            haccp: "HACCP",
          },
          deck: {
            subtitle: "航海与运营",
            title: "甲板<span class='highlight'>部门</span>",
            description:
              "专业的甲板高级船员和船员负责航行、货物操作和船舶安全。",
            positions: {
              master: {
                rank: "船长",
                salary: "$5,000 - $12,000",
                title: "船长",
                description: "船舶的总指挥，负责安全、航行和遵守国际法规。",
                requirement1: "船长执照（无限航区）",
                requirement2: "至少5年大副经验",
                requirement3: "STCW认证",
                requirement4: "有效的健康证书",
              },
              chiefOfficer: {
                rank: "大副",
                salary: "$4,000 - $8,000",
                title: "大副",
                description: "第二指挥官，负责货物操作、稳性和甲板部门管理。",
                requirement1: "大副执照",
                requirement2: "至少3年高级船员经验",
                requirement3: "货物处理经验",
                requirement4: "领导技能",
              },
              secondOfficer: {
                rank: "二副",
                salary: "$3,000 - $5,000",
                title: "二副",
                description: "负责航行、海图校正和通信设备维护。",
                requirement1: "值班驾驶员执照",
                requirement2: "航行经验",
                requirement3: "GMDSS证书",
                requirement4: "身体健康",
              },
              thirdOfficer: {
                rank: "三副",
                salary: "$2,500 - $4,000",
                title: "三副",
                description: "协助航行值班、安全设备维护和救生设备。",
                requirement1: "值班驾驶员执照",
                requirement2: "STCW基本培训",
                requirement3: "消防证书",
                requirement4: "入门级职位",
              },
              ratings: {
                rank: "甲板普通船员",
                salary: "$1,200 - $2,500",
                title: "甲板船员 (AB/OS)",
                description:
                  "负责甲板维护、系泊操作和值班职责的熟练水手和普通水手。",
                requirement1: "AB或OS证书",
                requirement2: "STCW基本安全培训",
                requirement3: "海上服务经验",
                requirement4: "身体健康",
              },
            },
            skills: {
              navigation: "航行与海图作业",
              cargo: "货物操作",
              safety: "安全管理",
              maintenance: "甲板维护",
              communication: "GMDSS通信",
              mooring: "系泊操作",
            },
          },
          engine: {
            subtitle: "推进与机械",
            title: "轮机<span class='highlight'>部门</span>",
            description:
              "负责推进系统、辅助机械和电气系统的熟练工程师和技术人员。",
            positions: {
              chiefEngineer: {
                rank: "轮机长",
                salary: "$6,000 - $12,000",
                title: "轮机长",
                description: "全面负责轮机部门的运营、维护和技术管理。",
                requirement1: "轮机长执照（无限航区）",
                requirement2: "至少5年大管轮经验",
                requirement3: "管理经验",
                requirement4: "预算技能",
              },
              secondEngineer: {
                rank: "大管轮",
                salary: "$4,500 - $8,000",
                title: "大管轮",
                description: "负责主机、维护计划和技术监督。",
                requirement1: "大管轮执照",
                requirement2: "至少3年轮机员经验",
                requirement3: "维护计划经验",
                requirement4: "技术监督技能",
              },
              thirdEngineer: {
                rank: "二管轮",
                salary: "$3,000 - $5,000",
                title: "二管轮",
                description: "负责辅助机械、锅炉和电气系统维护。",
                requirement1: "值班轮机员（轮机）",
                requirement2: "辅助系统经验",
                requirement3: "电气知识",
                requirement4: "故障排除技能",
              },
              fourthEngineer: {
                rank: "三管轮",
                salary: "$2,500 - $4,000",
                title: "三管轮",
                description: "协助值班和维护职责的入门级工程官员。",
                requirement1: "值班轮机员（轮机）",
                requirement2: "接受应届毕业生",
                requirement3: "基本工程知识",
                requirement4: "学习意愿",
              },
              ratings: {
                rank: "机舱普通船员",
                salary: "$1,200 - $2,500",
                title: "机舱船员",
                description:
                  "负责机舱运营和维护支持的加油工、机工和擦拭工。",
                requirement1: "机舱值班证书",
                requirement2: "STCW基本安全培训",
                requirement3: "机械能力",
                requirement4: "身体健康",
              },
            },
            skills: {
              mainEngine: "主机操作",
              electrical: "电气系统",
              maintenance: "预防性维护",
              lubrication: "润滑系统",
              boilers: "锅炉操作",
              pneumatic: "气动系统",
            },
          },
          hotel: {
            subtitle: "酒店与服务",
            title: "酒店<span class='highlight'>部门</span>",
            description:
              "提供卓越客户服务、烹饪体验和住宿管理的酒店专业人员。",
            positions: {
              hotelManager: {
                rank: "酒店经理",
                salary: "$4,000 - $8,000",
                title: "酒店经理",
                description: "全面负责酒店运营、宾客服务和部门管理。",
                requirement1: "酒店管理学位",
                requirement2: "至少5年酒店经验",
                requirement3: "领导和管理技能",
                requirement4: "卓越的客户服务",
              },
              executiveChef: {
                rank: "行政主厨",
                salary: "$3,500 - $6,500",
                title: "行政主厨",
                description:
                  "烹饪部门主管，负责菜单规划、食品质量控制和厨房管理。",
                requirement1: "烹饪艺术学位",
                requirement2: "至少8年厨房经验",
                requirement3: "菜单规划专业知识",
                requirement4: "食品安全认证",
              },
              restaurantManager: {
                rank: "餐厅经理",
                salary: "$2,500 - $4,500",
                title: "餐厅经理",
                description: "管理餐厅运营、服务质量、员工培训和宾客满意度。",
                requirement1: "酒店或相关学位",
                requirement2: "至少3年餐饮经验",
                requirement3: "服务培训技能",
                requirement4: "多语言优先",
              },
              housekeepingSupervisor: {
                rank: "客房主管",
                salary: "$1,800 - $3,000",
                title: "客房主管",
                description: "监督客房清洁、洗衣运营和住宿维护。",
                requirement1: "客房服务经验",
                requirement2: "监督技能",
                requirement3: "库存管理",
                requirement4: "注重细节",
              },
              stewards: {
                rank: "服务员",
                salary: "$1,200 - $2,200",
                title: "服务员 / 侍者",
                description: "提供餐饮服务、客房服务和公共区域清洁。",
                requirement1: "高中文凭",
                requirement2: "服务行业经验",
                requirement3: "良好的沟通技巧",
                requirement4: "客户服务导向",
              },
            },
            skills: {
              fnb: "餐饮服务",
              housekeeping: "卓越客房服务",
              guest: "宾客关系",
              butler: "管家服务",
              culinary: "烹饪艺术",
              language: "多语言技能",
            },
          },
          entertainment: {
            subtitle: "宾客活动与娱乐",
            title: "娱乐<span class='highlight'>部门</span>",
            description: "才华横溢的表演者、活动协调员和娱乐专业人士，创造难忘的宾客体验。",
            positions: {
              cruiseDirector: {
                rank: "邮轮总监",
                salary: "$4,000 - $7,000",
                title: "邮轮总监",
                description: "监督所有娱乐节目、活动和宾客参与计划。",
                requirement1: "娱乐管理学位",
                requirement2: "至少5年经验",
                requirement3: "公开演讲技巧",
                requirement4: "活动策划专长",
              },
              entertainers: {
                rank: "演艺人员",
                salary: "$2,000 - $5,000",
                title: "表演者与演艺人员",
                description: "提供舞台表演和娱乐的歌手、舞者、音乐家和综艺艺术家。",
                requirement1: "表演背景",
                requirement2: "需试镜",
                requirement3: "舞台表现力",
                requirement4: "表演多面性",
              },
              activityCoordinator: {
                rank: "活动协调员",
                salary: "$1,800 - $3,000",
                title: "活动协调员",
                description: "策划并主持宾客活动、游戏、讲习班和社交活动。",
                requirement1: "休闲或相关学位",
                requirement2: "活动策划经验",
                requirement3: "性格外向",
                requirement4: "多语言优先",
              },
              youthStaff: {
                rank: "青少年员工",
                salary: "$1,500 - $2,500",
                title: "青少年项目员工",
                description: "为年轻宾客提供儿童保育、青年活动和教育项目。",
                requirement1: "教育或儿童保育背景",
                requirement2: "儿童安全认证",
                requirement3: "创意活动策划",
                requirement4: "耐心和热情",
              },
            },
            skills: {
              performing: "舞台表演",
              games: "游戏主持",
              engagement: "宾客互动",
              music: "音乐才华",
              creative: "创意艺术",
              language: "多语言主持",
            },
          },
          recruitment: {
            subtitle: "加入我们的团队",
            title: "招聘<span class='highlight'>流程</span>",
            description: "我们结构化的招聘流程确保为每个职位选择最佳候选人。",
            step1: {
              title: "提交申请",
              description: "通过我们的在线门户提交申请及所需文件。",
              detail1: "填写在线申请表",
              detail2: "上传简历/CV",
              detail3: "提交证书副本",
              detail4: "提供护照尺寸照片",
            },
            step2: {
              title: "文件筛选",
              description: "我们的招聘团队审查所有申请和文件。",
              detail1: "证书验证",
              detail2: "经验核实",
              detail3: "体检检查",
              detail4: "背景调查",
            },
            step3: {
              title: "面试与评估",
              description: "入围候选人接受面试和技能评估。",
              detail1: "技术面试",
              detail2: "HR面试",
              detail3: "实操评估",
              detail4: "心理评估",
            },
            step4: {
              title: "培训与认证",
              description: "完成所需的培训和认证。",
              detail1: "STCW基本安全培训",
              detail2: "公司特定培训",
              detail3: "保安意识培训",
              detail4: "体格检查",
            },
            step5: {
              title: "派遣",
              description: "最终安置和上船旅行安排。",
              detail1: "签署合同",
              detail2: "旅行安排",
              detail3: "签证办理",
              detail4: "上船须知",
            },
            cta: {
              title: "准备好开始您的海事职业了吗？",
              description: "立即申请并加入我们的专业海员团队。我们提供具有竞争力的待遇和职业发展机会。",
            },
          },
          documents: {
            subtitle: "准备您的申请",
            title: "所需<span class='highlight'>文件</span>",
            category1: {
              title: "个人文件",
              item1: "护照 (至少2年有效期)",
              item2: "海员身份证件 (SID)",
              item3: "出生证明",
              item4: "结婚证 (如适用)",
              item5: "无犯罪记录证明",
            },
            category2: {
              title: "专业文件",
              item1: "适任证书 (COC)",
              item2: "STCW证书",
              item3: "培训证书",
              item4: "服务资历簿",
              item5: "推荐信",
            },
            category3: {
              title: "医疗与健康",
              item1: "有效健康适任证书",
              item2: "疫苗接种记录",
              item3: "牙科证明",
              item4: "视力检查报告",
              item5: "COVID-19疫苗证书",
            },
          },
        },

        // Contact Form Section
        contact: {
          form: {
            subtitle: "发送消息",
            title: "联系 <span class='highlight'>表单</span>",
            description: "请完整填写以下表单，我们的团队将在2小时内与您联系。",
            step1: "基本信息",
            step2: "服务详情",
            step3: "确认",
            step1Title: "您的联系信息",
            step1Desc: "请填写您的联系信息",
            step2Title: "您的服务需求",
            step2Desc: "选择您需要的服务",
            step3Title: "消息详情和附件",
            step3Desc: "如需要请添加消息和附件",
            fullName: "全名",
            email: "电子邮件",
            phone: "电话号码/WhatsApp",
            company: "公司名称",
            position: "职位",
            companyType: "公司类型",
            serviceType: "所需服务类型",
            vesselType: "船舶类型",
            urgency: "紧急程度",
            crewSize: "所需船员人数",
            budgetRange: "预算范围",
            subject: "消息主题",
            message: "请求详情",
            attachment: "文件附件（可选）",
            referral: "您是如何了解我们的？",
            prev: "上一步",
            next: "下一步",
            submit: "提交请求",
            successTitle: "请求已提交！",
            successMsg: "感谢您的请求。我们的团队将在2小时内通过您提供的电子邮件和电话与您联系。",
            requestId: "请求ID：",
            newRequest: "提交新请求",
          },
        },
      },
    };
  }

  initialize() {
    // Set initial language
    this.setLanguage(this.currentLang);

    // Setup language switcher buttons
    this.setupLanguageButtons();

    // Update page metadata
    this.updatePageMetadata();

    // Set current year in footer
    this.setCurrentYear();
  }

  setLanguage(lang) {
    if (!this.translations[lang]) {
      console.error(`Language ${lang} not found`);
      return;
    }

    this.currentLang = lang;
    localStorage.setItem("preferredLanguage", lang);

    // Update HTML lang attribute
    document.documentElement.lang = lang;

    // Update active language button
    this.updateLanguageButtons(lang);

    // Update all text elements
    this.updateAllTexts();

    // Update page metadata
    this.updatePageMetadata();
  }

  setupLanguageButtons() {
    document.querySelectorAll(".lang-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const lang = btn.getAttribute("data-lang");
        this.setLanguage(lang);
      });
    });
  }

  updateLanguageButtons(lang) {
    if (typeof document !== "undefined") {
      document.querySelectorAll(".lang-btn").forEach((btn) => {
        btn.classList.remove("active");
        if (btn.getAttribute("data-lang") === lang) {
          btn.classList.add("active");
        }
      });
    }
  }

  updateAllTexts() {
    // Get all elements with data-translate attribute
    const elements = document.querySelectorAll("[data-translate]");

    elements.forEach((element) => {
      const key = element.getAttribute("data-translate");
      const translation = this.getTranslation(key);

      if (translation) {
        this.applyTranslation(element, translation);
      } else {
        console.warn(`Translation not found for key: ${key}`);
      }
    });
  }

  getTranslation(key) {
    try {
      // Navigate through nested translation object
      const keys = key.split(".");
      let value = this.translations[this.currentLang];

      for (const k of keys) {
        if (value && value[k] !== undefined) {
          value = value[k];
        } else {
          // Try to get from English as fallback
          if (this.currentLang !== "en") {
            let fallbackValue = this.translations.en;
            for (const fallbackKey of keys) {
              if (fallbackValue && fallbackValue[fallbackKey] !== undefined) {
                fallbackValue = fallbackValue[fallbackKey];
              } else {
                return null;
              }
            }
            return fallbackValue;
          }
          return null;
        }
      }

      return value;
    } catch (error) {
      console.error(`Error getting translation for key: ${key}`, error);
      return null;
    }
  }

  applyTranslation(element, translation) {
    try {
      if (typeof translation === "string") {
        // Handle HTML content
        if (
          element.tagName === "INPUT" ||
          element.tagName === "TEXTAREA"
        ) {
          element.placeholder = translation;
        } else if (
          translation.includes("<span") ||
          translation.includes("<br") ||
          translation.includes("<i")
        ) {
          element.innerHTML = translation;
        } else {
          element.textContent = translation;
        }
      } else if (Array.isArray(translation)) {
        // Handle arrays
        if (element.tagName === "SELECT" || element.tagName === "UL") {
          element.innerHTML = "";
          translation.forEach((item, index) => {
            if (element.tagName === "SELECT") {
              const option = document.createElement("option");
              option.value = item.value || item;
              option.textContent = item.text || item;
              if (item.selected) option.selected = true;
              element.appendChild(option);
            } else if (element.tagName === "UL") {
              const li = document.createElement("li");
              li.textContent = item;
              element.appendChild(li);
            }
          });
        }
      } else if (typeof translation === "object") {
        // Handle object (for form inputs)
        if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
          if (translation.placeholder) {
            element.placeholder = translation.placeholder;
          }
          if (translation.value) {
            element.value = translation.value;
          }
          if (
            translation.label &&
            element.previousElementSibling &&
            element.previousElementSibling.tagName === "LABEL"
          ) {
            element.previousElementSibling.textContent = translation.label;
          }
        }
      }
    } catch (error) {
      console.error(`Error applying translation to element:`, element, error);
    }
  }

  updatePageMetadata() {
    try {
      // Update page title
      const titleTranslation = this.getTranslation("title");
      if (titleTranslation) {
        document.title = titleTranslation;
      }

      // Update meta description
      const metaDescription = document.querySelector(
        'meta[name="description"]'
      );
      if (metaDescription) {
        const description =
          this.getTranslation("metaDescription") ||
          "Professional maritime services including crew management, ship chandler, and vessel documentation for global shipping industry.";
        metaDescription.content = description;
      }

      // Update Open Graph title
      const ogTitle = document.querySelector('meta[property="og:title"]');
      if (ogTitle && titleTranslation) {
        ogTitle.content = titleTranslation;
      }

      // Update Open Graph description
      const ogDescription = document.querySelector(
        'meta[property="og:description"]'
      );
      if (ogDescription && metaDescription) {
        ogDescription.content = metaDescription.content;
      }
    } catch (error) {
      console.error("Error updating page metadata:", error);
    }
  }

  setCurrentYear() {
    const yearElements = document.querySelectorAll("#currentYear");
    const currentYear = new Date().getFullYear();
    yearElements.forEach((element) => {
      element.textContent = currentYear;
    });
  }

  // Public method to manually update translations
  updateTranslations(newTranslations) {
    Object.keys(newTranslations).forEach((lang) => {
      if (this.translations[lang]) {
        this.translations[lang] = {
          ...this.translations[lang],
          ...newTranslations[lang],
        };
      } else {
        this.translations[lang] = newTranslations[lang];
      }
    });
    this.updateAllTexts();
  }

  // Method to get current language
  getCurrentLanguage() {
    return this.currentLang;
  }

  // Method to add language dynamically
  addLanguage(langCode, translations) {
    this.translations[langCode] = translations;
    this.updateAllTexts();
  }
}

// Initialize translation system
document.addEventListener("DOMContentLoaded", () => {
  window.translationSystem = new TranslationSystem();
});

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = TranslationSystem;
}
