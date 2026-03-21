import  { useState, useEffect } from 'react';
import { useLocation } from "react-router-dom";
// eslint-disable-next-line no-unused-vars
import { motion } from 'framer-motion';


const MediaAndStories = () => {
const [activeTab, setActiveTab] = useState('news');
  const location = useLocation(); 

 useEffect(() => {
    if (location.hash) {
      const targetTab = location.hash.replace('#', '');
      
      
      setTimeout(() => {
        setActiveTab(targetTab);
      }, 0);

      setTimeout(() => {
        const el = document.querySelector(location.hash);
        if (el) {
          el.scrollIntoView({ behavior: "smooth" });
        }
      }, 100);
    }
  }, [location.hash]);



  
  const newsArticles = [
    {
      id: 1,
      tag: "Campaign",
      title: "Launch of New Educational Initiative in Rural Areas",
      excerpt: "We are excited to announce our new program aimed at bridging the digital divide by providing tablets and internet access to 50 schools.",
      date: "Oct 12, 2023",
      image: "about/news1.png"
    },
    {
      id: 2,
      tag: "Community",
      title: "Clean Water Project Reaches 10,000 Families",
      excerpt: "Our latest infrastructure project has successfully brought safe drinking water to remote villages in the northern region.",
      date: "Nov 05, 2023",
      image: "about/news3.png" // Make sure you have a 2nd image!
    },
    {
      id: 3,
      tag: "Environment",
      title: "Annual Tree Planting Drive Exceeds Goals",
      excerpt: "Volunteers gathered this weekend to plant over 5,000 saplings, contributing to our ongoing reforestation efforts.",
      date: "Dec 01, 2023",
      image: "about/news4.png" // Make sure you have a 3rd image!
    }
  ];


  const photosGal = [
    {
    id: 1,
    image: "gallery/1.png"
    },
    {
    id: 2,
    image: "gallery/2.png"
    },
    {
    id: 3,
    image: "gallery/3.png"
    },
    {
    id: 4,
    image: "gallery/4.png"
    },
    {
    id: 5,
    image: "gallery/5.png"
    },
    {
    id: 6,
    image: "gallery/6.png"
    },
    {
    id: 7,
    image: "gallery/7.png"
    },
    

  ]

  const pressCov = [
    {
        id: 1,
        tag: "The Daily Chronicle",
        datee: "Sep 15, 2023",
        title: "NGO Recognized for Exemplary Work in Sustainable Development",
        image: "gallery/1.png",
        para: "An independent review highlights the outstanding contributions made by the foundation in improving rural livelihood standards across 5 states, setting a benchmark for community-led initiatives..."
    },
    {
        id: 2,
        tag: "The Daily Chronicle",
        datee: "Sep 15, 2023",
        title: "NGO Recognized for Exemplary Work in Sustainable Development",
        image: "gallery/2.png",
        para: "An independent review highlights the outstanding contributions made by the foundation in improving rural livelihood standards across 5 states, setting a benchmark for community-led initiatives..."
    },
  ]






    return (
        <div className="bg-white min-h-screen ">
            {/* Hero Section */}
            <section className="bg-primary text-white py-20 px-4">
                <div className="max-w-7xl mx-auto text-center">
                    <motion.h1
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="text-4xl md:text-5xl font-bold mb-6"
                    >
                        Media & Stories
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.1 }}
                        className="text-lg md:text-xl max-w-3xl mx-auto"
                    >
                        Discover the stories of change, our latest news, and the visual journey of our impact.
                    </motion.p>
                </div>
            </section>

            {/* Navigation Tabs */}
            <section className="border-b border-gray-200 sticky top-20 bg-white z-40">
                <div className="max-w-3xl mx-auto px-4">
                    <div className="flex overflow-x-auto hide-scrollbar space-x-8 ">
                        {[
                            { id: 'news', label: 'News & Updates', icon: '📰' },
                            { id: 'photos', label: 'Photo Gallery', icon: '📸' },
                            { id: 'videos', label: 'Video Gallery', icon: '🎥' },
                            { id: 'press', label: 'Press Coverage', icon: '🗞️' },
                        ].map(tab => (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`py-4 px-2 whitespace-nowrap font-bold flex items-center gap-2 border-b-2 transition-colors ${activeTab === tab.id
                                        ? 'border-primary text-primary'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }`}
                            >
                                <span>{tab.icon}</span> {tab.label}
                            </button>
                        ))}
                    </div>
                </div>
            </section>

            {/* Content Area */}
            <section className="py-12 px-4 bg-gray-50 min-h-[60vh]">
                <div className="max-w-7xl mx-auto">

                    {/* News & Updates */}
                    {activeTab === 'news' && (
                        <motion.div
                            id="news"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                        >
                            {newsArticles.map((article) => (
                                  <div key={article.id} className="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                                  <div className="h-48 bg-gray-200 relative overflow-hidden">
                                     <div className="absolute inset-0 bg-linear-to-tr from-gray-300 to-gray-200 flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform duration-500">
                                       {/* Dynamically insert the image */}
                                     <img src={article.image} alt={article.title} className="w-full h-full object-cover" />
                               </div>
                             </div>
                            <div className="p-6">
                                {/* Dynamically insert the tag */}
                                <span className="text-xs font-bold text-accent uppercase tracking-wider">{article.tag}</span>
                                
                                {/* Dynamically insert the title */}
                                <h3 className="text-xl font-bold text-text-primary mt-2 mb-3 line-clamp-2">
                                    {article.title}
                                </h3>
                                
                                {/* Dynamically insert the excerpt */}
                                <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                                    {article.excerpt}
                                </p>
            
                                    <div className="flex justify-between items-center">
                                        {/* Dynamically insert the date */}
                                        <span className="text-xs text-gray-400">{article.date}</span>
                                        <button className="text-primary font-bold text-sm hover:underline">Read More →</button>
                                    </div>
                                </div>
                            </div>
                        ))}
                        </motion.div>
                    )}

                    {/* Photo Gallery */}
                    {activeTab === 'photos' && (
                        <motion.div
                            id="photos"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                        >
                            {photosGal.map(item => (
                                <div key={item.id} className="aspect-square bg-gray-200 rounded-xl overflow-hidden relative group cursor-pointer">
                                    <div className="absolute inset-0 bg-linear-to-br from-gray-300 to-gray-200 flex items-center justify-center text-gray-400 group-hover:scale-110 transition-transform duration-500">
                                         <img src={item.image} alt="" srcset="" className='w-full h-full object-cover' />
                                    </div>
                                    <div className="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span className="text-white font-bold bg-black/50 px-4 py-2 rounded-full backdrop-blur-sm">View</span>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    )}

                    {/* Video Gallery */}
                    {activeTab === 'videos' && (
                        <motion.div
                            id="videos"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                        >
                            {[1, 2, 3].map(item => (
                                <div key={item} className="bg-white rounded-2xl overflow-hidden shadow-sm group cursor-pointer">
                                    <div className="aspect-video bg-gray-800 relative">
                                        <div className="absolute inset-0 flex items-center justify-center">
                                            <div className="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                                <span className="text-white text-2xl ml-1">▶</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="p-4">
                                        <h3 className="font-bold text-text-primary mb-1">Impact Story: Transformative Agriculture {item}</h3>
                                        <p className="text-xs text-gray-500">3:45 mins • 1.2K views</p>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    )}

                    {/* Press Coverage */}
                    {activeTab === 'press' && (
                        <motion.div
                            id="press"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            className="space-y-6 max-w-4xl mx-auto"
                        >
                            {pressCov.map(item => (
                                <a key={item} href="#" className="flex flex-col md:flex-row gap-6 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                                    <div className="md:w-48 h-32 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 shrink-0 overflow-hidden">
                                        <div className="group-hover:scale-105 transition-transform">
                                            <img src={item.image} alt="" srcset="" className=' object-cover' />
                                        </div>
                                    </div>
                                    <div className="flex-1">
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold">{item.tag}</span>
                                            <span className="text-gray-400 text-sm">• {item.datee}</span>
                                        </div>
                                        <h3 className="text-xl font-bold text-text-primary mb-2 group-hover:text-primary transition-colors">
                                            {item.title}
                                        </h3>
                                        <p className="text-gray-600 text-sm">
                                            {item.para}
                                        </p>
                                    </div>
                                </a>
                            ))}
                        </motion.div>
                    )}

                </div>
            </section>

        </div>
    );
};

export default MediaAndStories;
