import streamlit as st
import requests
import time

# ✅ Streamlit App Title
st.title("🔐 Secure Pro App")

# ✅ Get token from URL
query_params = st.experimental_get_query_params()
token = query_params.get("token", [None])[0]

# ✅ Validate token with PHP backend
TOKEN_VALIDATION_URL = "https://login-sub-id.onrender.com/validate_token.php"
response = requests.get(f"{TOKEN_VALIDATION_URL}?token={token}")

if response.status_code == 200:
    if response.text == "VALID":
        # ✅ If token is valid, store login time
        st.session_state["login_time"] = time.time()

        # ✅ Auto Logout after 30 minutes
        if "login_time" in st.session_state:
            if time.time() - st.session_state["login_time"] > 1800:  # 30 minutes
                st.error("Session expired! Logging out...")
                st.markdown("<meta http-equiv='refresh' content='2; url=https://login-sub-id.onrender.com/index.php'>", unsafe_allow_html=True)
                st.stop()

        st.success("✅ Welcome to the Pro App!")
    else:
        st.error("❌ Session Expired! Please log in again.")
        st.markdown("<meta http-equiv='refresh' content='2; url=https://login-sub-id.onrender.com/index.php'>", unsafe_allow_html=True)
        st.stop()
else:
    st.error("⛔ Invalid Access! Redirecting...")
    st.markdown("<meta http-equiv='refresh' content='2; url=https://login-sub-id.onrender.com/index.php'>", unsafe_allow_html=True)
    st.stop()
