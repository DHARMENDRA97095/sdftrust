import { useEffect, useRef, useState } from "react";
import { Link } from "react-router-dom";

const API_URL = "http://localhost/sdftrust/backend/api/projects.php";
// const ADMIN_BASE_URL = "http://localhost/sdftrust/backend/admin/";
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

const makeImageUrl = (path) => {
  if (!path) return "https://via.placeholder.com/600x300?text=No+Image";

  if (typeof path !== "string") {
    return "https://via.placeholder.com/600x300?text=No+Image";
  }

  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }

  return `${ADMIN_BASE_URL}${path.replace(/^\/+/, "")}`;
};

function Herosection() {
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const scrollRef = useRef(null);

  useEffect(() => {
    const fetchProjects = async () => {
      try {
        const response = await fetch(API_URL);

        if (!response.ok) {
          throw new Error("Failed to fetch projects");
        }

        const data = await response.json();

        const rawProjects = Array.isArray(data)
          ? data
          : Array.isArray(data.data)
          ? data.data
          : [];

        const normalizedProjects = rawProjects.map((project, index) => ({
          id: project.id || index + 1,
          title: project.title || "Untitled Project",
          para:
            project.message ||
            project.description ||
            "No project description available.",
          date: project.date || project.created_at || "",
          img: makeImageUrl(project.image || project.image_url),
          link: project.link || `/projects/${project.slug || project.id || index + 1}`,
        }));

        setProjects(normalizedProjects);
      } catch (err) {
        console.error("Project fetch error:", err);
        setError(err.message || "Failed to load projects");
      } finally {
        setLoading(false);
      }
    };

    fetchProjects();
  }, []);

  useEffect(() => {
    const container = scrollRef.current;
    if (!container || projects.length === 0) return;

    let animationFrame;
    let isPaused = false;
    const speed = 0.5;

    const handleMouseEnter = () => {
      isPaused = true;
    };

    const handleMouseLeave = () => {
      isPaused = false;
    };

    container.addEventListener("mouseenter", handleMouseEnter);
    container.addEventListener("mouseleave", handleMouseLeave);

    const scroll = () => {
      if (!container) return;

      if (!isPaused) {
        container.scrollTop += speed;

        if (container.scrollTop >= container.scrollHeight / 2) {
          container.scrollTop = 0;
        }
      }

      animationFrame = requestAnimationFrame(scroll);
    };

    animationFrame = requestAnimationFrame(scroll);

    return () => {
      if (animationFrame) cancelAnimationFrame(animationFrame);
      container.removeEventListener("mouseenter", handleMouseEnter);
      container.removeEventListener("mouseleave", handleMouseLeave);
    };
  }, [projects]);

  return (
    <section className="relative bg-[#F9F6EA] overflow-hidden pb-10">
      <div className="absolute inset-0 z-0">
        <div
          className="w-full h-full bg-cover bg-center"
          style={{ backgroundImage: "url('/banner/hero.png')" }}
        ></div>
        <div className="absolute inset-0 bg-black/30"></div>
      </div>

      <div className="relative z-10 w-[95%] mx-auto min-h-[600px] flex items-center">
        <div className="max-w-2xl text-white pl-6 md:pl-10">
          <h1 className="text-4xl md:text-6xl font-bold mb-6">
            <span className="bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-green-500">
              Empowering
            </span>{" "}
            Communities, <br />
            Sustaining the Future
          </h1>

          <p className="mb-6">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
          </p>

          <Link
            to="/about"
            className="inline-block bg-yellow-500 hover:bg-yellow-600 text-black px-8 py-3 rounded-full shadow-lg"
          >
            Learn More →
          </Link>
        </div>

        <div className="hidden lg:block absolute right-6 top-1/2 -translate-y-1/2 w-[300px]">
          <div className="border border-white/30 rounded-2xl p-2 backdrop-blur-md">
            <h3 className="text-white font-bold mb-3">Incoming Projects</h3>

            {loading ? (
              <div className="text-white text-sm p-3">Loading projects...</div>
            ) : error ? (
              <div className="text-red-200 text-sm p-3">{error}</div>
            ) : projects.length === 0 ? (
              <div className="text-white text-sm p-3">No projects found.</div>
            ) : (
              <div
                ref={scrollRef}
                className="h-[500px] overflow-hidden space-y-4 p-2"
              >
                {[...projects, ...projects].map((project, index) => (
                  <a
                    key={`${project.id}-${index}`}
                    href={project.link}
                    className="block bg-white rounded-lg overflow-hidden hover:shadow-lg transition group"
                  >
                    <img
                      src={project.img}
                      alt={project.title}
                      className="w-full h-28 object-cover group-hover:scale-105 transition"
                      onError={(e) => {
                        e.currentTarget.src =
                          "https://via.placeholder.com/600x300?text=Image+Not+Found";
                      }}
                    />

                    <div className="p-2">
                      <h4 className="text-xs font-semibold">{project.title}</h4>

                      {project.date && (
                        <p className="text-[10px] text-gray-500">
                          {new Date(project.date).toLocaleDateString()}
                        </p>
                      )}

                      <p className="text-[10px] text-gray-600 line-clamp-2">
                        {project.para}
                      </p>
                    </div>
                  </a>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      <div className="absolute bottom-0 w-full overflow-hidden leading-none z-10">
        <svg
          className="w-full h-20 md:h-18 lg:h-36"
          viewBox="0 0 1440 320"
          preserveAspectRatio="none"
        >
          <path
            fill="#F9F6EA"
            d="M0,160L48,176C96,192,192,224,288,218.7C384,213,480,171,576,149.3C672,128,768,128,864,149.3C960,171,1056,213,1152,229.3C1248,245,1344,235,1392,229.3L1440,224L1440,320L0,320Z"
          ></path>
        </svg>
      </div>
    </section>
  );
}

export default Herosection;