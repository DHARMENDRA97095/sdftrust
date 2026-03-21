import { Link } from 'react-router-dom';
import { useState } from 'react';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [activeMobileMenu, setActiveMobileMenu] = useState(null);

  const toggleMobileMenu = (menu) => {
    setActiveMobileMenu(activeMobileMenu === menu ? null : menu);
  };





  const menuItems = [
    {
      name: 'About',
      path: '/about',
      hasDropdown: true,
      dropdownItems: [
        { label: 'Who We Are', path: '/about#who-we-are', icon: '💡' },
        { label: 'Leadership', path: '/about#leadership', icon: '👥' },
        { label: 'Our Approach', path: '/about#approach', icon: '🎯' },
        { label: 'Partners', path: '/about#partners', icon: '🤝' },
        { label: 'FAQ', path: '/about#fAq', icon: '🙋' },
      ],
    },
    {
      name: 'Programs',
      path: '/programs',
      hasDropdown: true,
      dropdownItems: [
        { label: 'Health & Nutrition', path: '/programs#health', icon: '🏥' },
        { label: 'Education', path: '/programs#education', icon: '📚' },
        { label: 'Livelihoods', path: '/programs#livelihoods', icon: '💼' },
        { label: 'Women Empowerment', path: '/programs#women', icon: '👩🏽‍🎓' },
        { label: 'Agriculture', path: '/programs#agriculture', icon: '🌾' },
        { label: 'Environment & WASH', path: '/programs#environment', icon: '🚰' },
      ],
    },
    {
      name: 'Our Work',
      path: '/projects',
      hasDropdown: true,
      dropdownItems: [
        { label: 'Ongoing Projects', path: '/projects#ongoing', icon: '🏢' },
        { label: 'State-wise Listings', path: '/projects#listings', icon: '🗺️' },
        { label: 'Impact Snapshot', path: '/projects#impact', icon: '📊' },
      ],
    },
    {
      name: 'Publications',
      path: '/publications',
      hasDropdown: true,
      dropdownItems: [
        { label: 'Annual Reports', path: '/publications#annual-reports', icon: '📈' },
        { label: 'Case Studies', path: '/publications#case-studies', icon: '📝' },
        { label: 'Photo Galleries', path: '/publications#galleries', icon: '🖼️' },
      ],
    },
    {
      name: 'Media & Stories',
      path: '/media',
      hasDropdown: true,
      dropdownItems: [
        { label: 'News & Updates', path: '/media#news', icon: '📰' },
        { label: 'Photo Gallery', path: '/media#photos', icon: '📸' },
        { label: 'Video Gallery', path: '/media#videos', icon: '🎥' },
        { label: 'Press Coverage', path: '/media#press', icon: '🗞️' },
      ],
    },
    {
      
      name: 'Get Involved',
      path: '/get-involved',
      hasDropdown: true,
      dropdownItems: [
        { label: 'Volunteer With Us', path: '/get-involved#volunteer', icon: '🤝' },
        { label: 'Careers', path: '/get-involved#careers', icon: '💼' },
        { label: 'Contact Us', path: '/contact', icon: '📞' },
      ],
    },
  ];

  return (
    <nav className="bg-white backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-20">
          {/* Logo Section */}
          <div className="flex items-center">
            <Link to="/" className="flex items-center gap-2">
              <div className=" p-2 rounded-full flex items-center justify-center size-60">
                <img src="logo/logo-bg.png" alt="" srcset="" />
              </div>

            </Link>
          </div>

          {/* Desktop Navigation (Mega Menu) */}
          <div className="hidden lg:flex items-center space-x-1">
            {menuItems.map((item) => (
              <div key={item.name} className="relative group px-3 py-6">
                <Link
                  to={item.path}
                  className="text-text-primary hover:text-primary font-bold text-sm transition-colors flex items-center gap-1"
                >
                  {item.name}
                  {item.hasDropdown && <span className="text-xs text-gray-400 group-hover:rotate-180 transition-transform">▼</span>}
                </Link>

                {/* Dropdown Content */}
                {item.hasDropdown && (
                  <div className="absolute left-1/2 -translate-x-1/2 top-full w-64 bg-white shadow-xl border border-gray-100 rounded-xl opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-300 overflow-hidden z-50">
                    <div className="p-2">
                      {item.dropdownItems.map((subItem) => (
                        <Link
                          key={subItem.label}
                          to={subItem.path}
                          className="flex items-center gap-3 px-4 py-3 hover:bg-bg-color rounded-lg transition-colors group/link"
                        >
                          <span className="text-xl group-hover/link:scale-110 transition-transform">{subItem.icon}</span>
                          <span className="text-sm font-bold text-gray-700 group-hover/link:text-primary">{subItem.label}</span>
                        </Link>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            ))}
            <div className="pl-4">
              <Link
                to="/donate"
                className="bg-accent hover:bg-[#237586] text-white px-6 py-2.5 rounded-full font-bold transition-all shadow-sm hover:-translate-y-0.5 hover:shadow-md ml-2"
              >
                Donate
              </Link>
            </div>
          </div>

          {/* Mobile menu button */}
          <div className="flex items-center lg:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              className="text-text-primary hover:text-primary p-2 focus:outline-none"
            >
              {isOpen ? <span className="text-2xl font-bold">✕</span> : <span className="text-2xl font-bold">☰</span>}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Navigation (Accordion) */}
      <div className={`lg:hidden transition-all duration-300 ease-in-out overflow-hidden ${isOpen ? 'max-h-screen border-t border-gray-100' : 'max-h-0'}`}>
        <div className="px-4 pt-2 pb-6 space-y-1 bg-white shadow-inner max-h-[calc(100vh-80px)] overflow-y-auto">
          {menuItems.map((item) => (
            <div key={item.name} className="border-b border-gray-50 last:border-0">
              {item.hasDropdown ? (
                <>
                  <button
                    onClick={() => toggleMobileMenu(item.name)}
                    className="w-full flex items-center justify-between px-3 py-4 text-base font-bold text-text-primary focus:outline-none"
                  >
                    {item.name}
                    <span className={`text-sm text-gray-400 transition-transform ${activeMobileMenu === item.name ? 'rotate-180 text-primary' : ''}`}>▼</span>
                  </button>
                  <div className={`overflow-hidden transition-all duration-300 ease-in-out bg-bg-color/50 rounded-lg ${activeMobileMenu === item.name ? 'max-h-96 mb-2 opacity-100' : 'max-h-0 opacity-0'}`}>
                    {item.dropdownItems.map((subItem) => (
                      <Link
                        key={subItem.label}
                        to={subItem.path}
                        className="flex items-center gap-3 px-6 py-3 text-sm font-medium text-gray-600 hover:text-primary hover:bg-white transition-colors"
                        onClick={() => setIsOpen(false)}
                      >
                        <span className="text-lg">{subItem.icon}</span> {subItem.label}
                      </Link>
                    ))}
                  </div>
                </>
              ) : (
                <Link
                  to={item.path}
                  className="block px-3 py-4 text-base font-bold text-text-primary"
                  onClick={() => setIsOpen(false)}
                >
                  {item.name}
                </Link>
              )}
            </div>
          ))}
          <div className="pt-6 pb-2">
            <Link
              to="/donate"
              className="block w-full text-center bg-accent text-white px-6 py-4 rounded-xl font-bold shadow-md"
              onClick={() => setIsOpen(false)}
            >
              ❤️ Donate Now
            </Link>
          </div>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
